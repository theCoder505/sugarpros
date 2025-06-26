const express = require('express');
const WebSocket = require('ws');
const http = require('http');

const app = express();
const port = 3000;

const server = http.createServer(app);

const wss = new WebSocket.Server({ 
    server,
    clientTracking: true
});

// Store clients by user ID with additional metadata
const clients = new Map();

// Track last active timestamps
const lastActive = new Map();

wss.on('connection', (ws, req) => {
    // Extract user ID from query parameters
    const url = new URL(req.url, `ws://${req.headers.host}`);
    const userId = url.searchParams.get('userId');

    if (!userId) {
        ws.close(1008, 'User ID required');
        return;
    }

    console.log(`New client connected: ${userId}`);
    
    // Store client with connection time
    clients.set(userId, {
        ws,
        connectedAt: new Date()
    });
    
    // Update last active time
    lastActive.set(userId, new Date());

    // Send welcome message and current online status
    ws.send(JSON.stringify({
        type: 'system',
        message: 'Connected to chat server',
        timestamp: new Date().toISOString()
    }));

    // Notify all clients about the new connection
    broadcastOnlineStatus();

    ws.on('message', (message) => {
        try {
            const data = JSON.parse(message);
            console.log(`Received from ${userId}:`, data);

            // Update last active time on any message
            lastActive.set(userId, new Date());

            // Handle different message types
            switch (data.type) {
                case 'message':
                case 'image':
                    // Validate message data
                    if (!data.receiverId || !data.message) {
                        console.warn('Invalid message format from', userId);
                        return;
                    }

                    // Send to recipient if they're connected
                    if (clients.has(data.receiverId)) {
                        const recipient = clients.get(data.receiverId);
                        if (recipient.ws.readyState === WebSocket.OPEN) {
                            recipient.ws.send(JSON.stringify({
                                type: data.type,
                                senderId: userId,
                                message: data.message,
                                timestamp: new Date().toISOString()
                            }));
                        }
                    }
                    break;

                case 'typing':
                    // Validate typing data
                    if (!data.receiverId || typeof data.isTyping !== 'boolean') {
                        console.warn('Invalid typing indicator from', userId);
                        return;
                    }

                    // Forward typing indicator to recipient
                    if (clients.has(data.receiverId)) {
                        const recipient = clients.get(data.receiverId);
                        if (recipient.ws.readyState === WebSocket.OPEN) {
                            recipient.ws.send(JSON.stringify({
                                type: 'typing',
                                senderId: userId,
                                isTyping: data.isTyping
                            }));
                        }
                    }
                    break;

                case 'seen':
                    // Validate seen notification
                    if (!data.receiverId) {
                        console.warn('Invalid seen notification from', userId);
                        return;
                    }

                    // Forward seen indicator to recipient
                    if (clients.has(data.receiverId)) {
                        const recipient = clients.get(data.receiverId);
                        if (recipient.ws.readyState === WebSocket.OPEN) {
                            recipient.ws.send(JSON.stringify({
                                type: 'seen',
                                senderId: userId,
                                receiverId: data.receiverId,
                                timestamp: new Date().toISOString()
                            }));
                        }
                    }
                    break;

                case 'getOnlineStatus':
                    // Send current online status to requester
                    if (clients.has(userId)) {
                        const requester = clients.get(userId);
                        if (requester.ws.readyState === WebSocket.OPEN) {
                            requester.ws.send(JSON.stringify({
                                type: 'onlineStatus',
                                onlineUsers: Array.from(clients.keys()),
                                timestamp: new Date().toISOString()
                            }));
                        }
                    }
                    break;

                case 'ping':
                    // Respond to ping requests
                    ws.send(JSON.stringify({
                        type: 'pong',
                        timestamp: new Date().toISOString()
                    }));
                    break;

                default:
                    console.warn('Unknown message type from', userId, data.type);
            }
        } catch (error) {
            console.error('Error processing message:', error);
        }
    });

    ws.on('close', () => {
        console.log(`Client disconnected: ${userId}`);
        clients.delete(userId);
        lastActive.delete(userId);
        broadcastOnlineStatus();
    });

    ws.on('error', (error) => {
        console.error(`WebSocket error for user ${userId}:`, error);
    });
});

// Broadcast online status to all connected clients
function broadcastOnlineStatus() {
    const onlineUsers = Array.from(clients.keys());
    const payload = JSON.stringify({
        type: 'onlineStatus',
        onlineUsers: onlineUsers,
        timestamp: new Date().toISOString()
    });

    clients.forEach((client, userId) => {
        if (client.ws.readyState === WebSocket.OPEN) {
            client.ws.send(payload);
        }
    });
}

// Cleanup inactive connections
setInterval(() => {
    const now = new Date();
    const inactiveThreshold = 1000 * 60 * 5; // 5 minutes
    
    clients.forEach((client, userId) => {
        const lastActiveTime = lastActive.get(userId);
        if (lastActiveTime && (now - lastActiveTime) > inactiveThreshold) {
            console.log(`Closing inactive connection for ${userId}`);
            client.ws.close(1000, 'Inactive connection');
            clients.delete(userId);
            lastActive.delete(userId);
        }
    });
}, 1000 * 60); // Check every minute

// Start the server
server.listen(port, () => {
    console.log(`WebSocket server running on ws://localhost:${port}`);
});

// Handle graceful shutdown
process.on('SIGTERM', () => {
    console.log('Shutting down WebSocket server...');
    
    // Close all client connections
    clients.forEach((client, userId) => {
        client.ws.close(1001, 'Server shutting down');
    });
    
    // Close the WebSocket server
    wss.close(() => {
        server.close(() => {
            console.log('WebSocket server closed');
            process.exit(0);
        });
    });
});