<div class="p-4 bg-white rounded-lg shadow">
    {{-- Judul Section --}}
    <h3 class="text-lg font-medium mb-4">Document Preview</h3>

    {{-- Kondisi jika file ada --}}
    @if($getFileUrl())
    {{-- Tampilan untuk PDF --}}
    @if(str_ends_with($getFileUrl(), '.pdf'))
    <iframe
        src="{{ $getFileUrl() }}"
        class="w-full h-96 border"
        title="Document Preview"
        frameborder="0"></iframe>

    {{-- Tampilan untuk gambar (JPG, PNG, dll) --}}
    @else
    <img
        src="{{ $getFileUrl() }}"
        alt="Document Preview"
        class="max-w-full h-auto mb-4 rounded"
        onerror="this.onerror=null;this.src='{{ asset('images/file-error.png') }}'">
    @endif

    {{-- Tombol Download --}}
    <div class="mt-4 flex justify-between items-center">
        <a
            href="{{ $getFileUrl() }}"
            download
            class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
            </svg>
            Download Document
        </a>

        {{-- Info Nama File --}}
        <span class="text-sm text-gray-500">
            {{ basename($record->doc_permit) }}
        </span>
    </div>

    {{-- Kondisi jika file tidak ada --}}
    @else
    <div class="bg-red-50 border-l-4 border-red-400 p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm text-red-700">
                    @if(!$record->doc_permit)
                    <strong>No document uploaded</strong> - Please upload a document first.
                    @else
                    <strong>Document not found</strong> - The file "{{ basename($record->doc_permit) }}" is missing from storage.
                    @endif
                </p>
            </div>
        </div>
    </div>
    @endif
</div>