<div>
    <div class="flex flex-col items-center gap-6">
        <!-- Photo Preview -->
        <div class="flex flex-col items-center gap-4">
            <div id="photoPreview" class="w-40 h-40 rounded-full bg-gradient-to-br from-green-600 to-green-700 flex items-center justify-center text-5xl text-white font-bold shadow-lg overflow-hidden flex-shrink-0">
                @if (Auth::user()->foto)
                    <img src="{{ Storage::url(Auth::user()->foto) }}?t={{ time() }}" alt="Profile Photo" class="w-full h-full object-cover">
                @else
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                @endif
            </div>
            <p class="text-sm text-gray-600 text-center">
                Format: JPG, PNG, GIF<br>
                Ukuran maksimal: 2MB
            </p>
        </div>

        <!-- Upload Form -->
        <div class="flex-1">
            <form id="photoForm" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <input type="file" id="fotoInput" name="foto" accept="image/*" class="hidden">
                
                <div class="space-y-3">
                    <button type="button" onclick="document.getElementById('fotoInput').click()" class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium">
                        Pilih Foto
                    </button>
                    <button type="submit" class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium">
                        Upload Foto
                    </button>
                </div>

                <div id="uploadMessage" class="hidden p-3 rounded-lg text-center text-sm font-medium"></div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('fotoInput').addEventListener('change', function(e) {
    const file = this.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(event) {
            const previewDiv = document.getElementById('photoPreview');
            previewDiv.innerHTML = `<img src="${event.target.result}" alt="Preview" class="w-full h-full object-cover">`;
        };
        reader.readAsDataURL(file);
    }
});

document.getElementById('photoForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const fileInput = document.getElementById('fotoInput');
    const file = fileInput.files[0];
    
    if (!file) {
        showMessage('Silakan pilih file foto terlebih dahulu', 'error');
        return;
    }

    const formData = new FormData();
    formData.append('foto', file);
    formData.append('_token', document.querySelector('input[name="_token"]').value);

    const messageDiv = document.getElementById('uploadMessage');

    try {
        const response = await fetch('{{ route("student.update-profile-photo") }}', {
            method: 'POST',
            body: formData,
        });

        const data = await response.json();

        if (response.ok) {
            showMessage(data.message, 'success');
            // Update photo preview
            document.getElementById('photoPreview').innerHTML = `<img src="${data.foto}?t=${Date.now()}" alt="Profile Photo" class="w-full h-full object-cover">`;
            fileInput.value = '';
            
            // Reload page after 1.5 seconds to update all photo instances
            setTimeout(() => {
                location.reload();
            }, 1500);
        } else {
            showMessage(data.error || 'Gagal mengupload foto', 'error');
        }
    } catch (error) {
        showMessage('Terjadi kesalahan: ' + error.message, 'error');
    }

    function showMessage(message, type) {
        messageDiv.classList.remove('hidden');
        messageDiv.textContent = message;
        messageDiv.className = 'p-3 rounded-lg text-center text-sm font-medium';
        
        if (type === 'success') {
            messageDiv.classList.add('bg-green-100', 'text-green-700');
        } else {
            messageDiv.classList.add('bg-red-100', 'text-red-700');
        }

        setTimeout(() => {
            messageDiv.classList.add('hidden');
        }, 5000);
    }
});
</script>
