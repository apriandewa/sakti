
    function slugify(text) {
        return text
            .toString()                 // pastikan string
            .normalize('NFD')           // handle huruf dengan aksen
            .replace(/[\u0300-\u036f]/g, '') 
            .toLowerCase()
            .trim()
            .replace(/[^a-z0-9]+/g, '-') // ganti non-alfanumerik jadi -
            .replace(/^-+|-+$/g, '');    // hapus - di awal/akhir
    }

    document.getElementById('nama').addEventListener('keyup', function() {
        let nama = this.value;
        document.getElementById('slug').value = slugify(nama);
    });