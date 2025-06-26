<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Subscription Plans OTP</title>
        <link rel="shortcut icon" href="{{ asset('assets/image/logo.png') }}" type="image/x-icon">

        <script src="https://cdn.tailwindcss.com"></script>
    </head>
    <body>
        <section
            class="flex flex-col items-center justify-center min-h-screen px-4 bg-white"
        >
            <div class="mb-6">
                <img src="{{ asset('assets/image/logo.png') }}" alt="SugarPros" class="h-10 mx-auto" />
            </div>

            <div class="w-full max-w-md text-center">
                <h2 class="text-[16px] text-[#525252]">
                    Verify Your Email Address
                </h2>
                <h1 class="mt-3 text-[28px] font-semibold text-[#121212]">
                    Enter the OTP sent to your Email
                </h1>

                <form class="mt-6 space-y-4">
                    <div class="text-left">
                        <label
                            for="otp"
                            class="block mb-1 text-sm font-semibold text-[#000]"
                            >OTP Code</label
                        >
                        <input
                            id="otp"
                            type="text"
                            placeholder="Enter Code Here"
                            class="w-full px-4 py-2 border rounded-md border-slate-300 focus:ring-2 focus:ring-[#2889AA] focus:outline-none"
                        />
                    </div>

                    <button
                        type="submit"
                        class="w-full py-2 font-semibold text-white transition rounded-md bg-[#2889AA] hover:bg-opacity-90 text-[18px] "
                    >
                        Confirm
                    </button>
                </form>

                <p class="mt-4 text-sm text-[#3E3E3E]">
                    Already have an account?
                    <a href="#" class="text-[#2889AA] hover:underline">Log in</a>
                </p>
            </div>

            <footer
                class="absolute w-full space-x-4 text-xs text-center bottom-4 text-[#747474]"
            >
                <a href="#" class="hover:underline">Help and Support</a>
                <a href="#" class="hover:underline">Privacy Policy</a>
                <a href="#" class="hover:underline">Terms And Conditions</a>
            </footer>
        </section>
    </body>
</html>
