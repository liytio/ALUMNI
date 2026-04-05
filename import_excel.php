<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Impor Data Alumni</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-xl mx-auto bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-2xl font-bold mb-4 text-gray-800">Impor Data Alumni (Excel)</h2>
        
        <form action="proses_import.php" method="POST" enctype="multipart/form-data" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Pilih File Excel (.xlsx) Maksimal 10MB</label>
                <input type="file" name="file_excel" accept=".xlsx" required 
                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2">
            </div>
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded w-full">
                Mulai Impor Data
            </button>
        </form>
    </div>
</body>
</html>