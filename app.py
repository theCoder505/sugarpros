from fastapi import FastAPI, File, UploadFile
from fastapi.middleware.cors import CORSMiddleware
import torch
from transformers import AutoModelForCausalLM, AutoTokenizer, pipeline
import whisper
import tempfile
import os

app = FastAPI()

# CORS middleware
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_methods=["*"],
    allow_headers=["*"],
)

# Load models (do this once at startup)
whisper_model = None
soap_model = None
tokenizer = None

@app.on_event("startup")
async def load_models():
    global whisper_model, soap_model, tokenizer
    
    # Load Whisper model
    whisper_model = whisper.load_model("large-v3")
    
    # Load SOAP model
    model_path = "path/to/your/mediSOAP_models"
    soap_model = AutoModelForCausalLM.from_pretrained(model_path)
    tokenizer = AutoTokenizer.from_pretrained(model_path)

@app.post("/generate_soap")
async def generate_soap(audio: UploadFile = File(...)):
    # Save uploaded audio to temp file
    with tempfile.NamedTemporaryFile(delete=False, suffix=".wav") as temp_audio:
        content = await audio.read()
        temp_audio.write(content)
        temp_audio_path = temp_audio.name

    try:
        # Transcribe audio
        result = whisper_model.transcribe(temp_audio_path)
        transcript = result["text"]

        # Generate SOAP notes
        prompt = f"Convert the following medical conversation to SOAP notes:\n\n{transcript}\n\nSOAP Notes:"
        
        inputs = tokenizer(prompt, return_tensors="pt", max_length=2048, truncation=True)
        
        with torch.no_grad():
            outputs = soap_model.generate(
                inputs.input_ids,
                max_length=2048,
                temperature=0.7,
                do_sample=True,
                pad_token_id=tokenizer.eos_token_id
            )
        
        soap_notes = tokenizer.decode(outputs[0], skip_special_tokens=True)
        soap_notes = soap_notes.replace(prompt, "").strip()

        return {
            "transcript": transcript,
            "soap_notes": soap_notes
        }

    finally:
        # Clean up temp file
        os.unlink(temp_audio_path)

if __name__ == "__main__":
    import uvicorn
    uvicorn.run(app, host="0.0.0.0", port=8000)