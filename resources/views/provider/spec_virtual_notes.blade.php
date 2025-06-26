@extends('layouts.provider')

@section('title', 'Virtual Notes')

@section('link')


@endsection

@section('style')


@endsection


@section('content')
    @include('layouts.provider_header')

    <div class="min-h-screen p-6 bg-gray-100">
        <div class="min-h-screen bg-[#f4f6f8] p-4">
            <div class="flex flex-col gap-3 mb-4 md:flex-row md:items-center md:justify-between">
                <h1 class="text-xl font-semibold text-[#000000]">
                    Virtual Notes
                </h1>
            </div>

            <div class="py-5 bg-gray-100 ">
                <div class="p-8 mx-auto overflow-hidden bg-white shadow-md rounded-xl">
                    <form class="space-y-6" action="/provider/update-virtual-notes" method="POST">
                        @csrf
                        <input type="hidden" name="appointment_uid" value="{{ $appointment_uid }}">
                        <input type="hidden" name="note_id" value="{{ $note_id }}">

                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700">Add Virtual Note</label>
                            <textarea name="main_note"
                                class="w-full  bg-white placeholder:text-[#A3A3A3]  px-3 py-2 mt-1 border  border-gray-300 rounded-md outline-none resize-none"
                                id="" placeholder="Type hare" cols="30" rows="6" required>{{ $main_note }}</textarea>
                        </div>

                        <div class="flex gap-4 justify-end">
                            <button type="submit"
                                class="bg-blue-500 py-2 px-4 text-white w-full rounded-lg text-md max-w-[100px]">
                                Save
                            </button>
                            <button type="button"
                                onclick="if(confirm('Are you sure you want to delete this lab?')){ this.form.action='/provider/delete-virtual-notes/{{ $note_id }}'; this.form.submit(); }"
                                class="bg-red-500 py-2 px-4 text-white w-full rounded-lg text-md max-w-[100px]">
                                Delete
                            </button>
                        </div>
                    </form>
                </div>
            </div>





        </div>



    @endsection

    @section('script')



    @endsection
