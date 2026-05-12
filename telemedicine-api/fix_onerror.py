import sys
path = 'c:/andra/skripsi/telemedicine-api/resources/views/pasien/obat.blade.php'
with open(path, 'r', encoding='utf-8') as f:
    c = f.read()
c = c.replace('onerror="this.src=', 'onerror="this.onerror=null;this.src=')
with open(path, 'w', encoding='utf-8') as f:
    f.write(c)
print("Done")
