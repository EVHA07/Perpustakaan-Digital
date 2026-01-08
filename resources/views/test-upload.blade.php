<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Upload</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen py-8">
    <div class="max-w-2xl mx-auto">
        <h1 class="text-3xl font-bold mb-8 text-center">Test Upload File</h1>

        <div class="bg-white rounded-lg shadow-md p-6">
            <form action="/test-upload" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Judul</label>
                    <input type="text" name="judul" value="Test Book" class="w-full px-3 py-2 border rounded-lg">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Cover Image (Max 2MB)</label>
                    <input type="file" name="cover_image" accept="image/jpeg,image/png,image/jpg" class="w-full">
                    <p class="text-sm text-gray-500 mt-1">Format: JPEG, PNG, JPG</p>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">File Buku (Max 10MB)</label>
                    <input type="file" name="file_path" accept=".pdf,.epub" class="w-full">
                    <p class="text-sm text-gray-500 mt-1">Format: PDF, EPUB</p>
                </div>

                <button type="submit" class="w-full bg-blue-500 text-white py-3 rounded-lg hover:bg-blue-600">
                    Test Upload
                </button>
            </form>
        </div>

        <div class="mt-8 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <h3 class="font-bold text-yellow-800 mb-2">Checklist:</h3>
            <ul class="list-disc list-inside text-yellow-700 space-y-1">
                <li>Storage directories: {{ file_exists(storage_path('app/public/books/covers')) ? '✅ Exists' : '❌ Missing' }}</li>
                <li>Covers directory: {{ file_exists(storage_path('app/public/books/covers')) ? '✅ Exists' : '❌ Missing' }}</li>
                <li>Files directory: {{ file_exists(storage_path('app/public/books/files')) ? '✅ Exists' : '❌ Missing' }}</li>
                <li>Storage link: {{ is_link(public_path('storage')) ? '✅ Linked' : '❌ Not linked' }}</li>
                <li>PHP Upload Max: {{ ini_get('upload_max_filesize') }}</li>
                <li>PHP Post Max: {{ ini_get('post_max_size') }}</li>
            </ul>
        </div>

        <div class="mt-4 bg-blue-50 border border-blue-200 rounded-lg p-4">
            <h3 class="font-bold text-blue-800 mb-2">PHP Info:</h3>
            <pre class="text-sm text-blue-700">
Max Execution Time: {{ ini_get('max_execution_time') }}s
Memory Limit: {{ ini_get('memory_limit') }}
File Uploads: {{ ini_get('file_uploads') ? 'On' : 'Off' }}
            </pre>
        </div>

        <div class="mt-8 text-center">
            <a href="/admin/books" class="text-blue-500 hover:underline">← Back to Admin</a>
        </div>
    </div>
</body>
</html>
