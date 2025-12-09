# Dokumentasi Error -- Tugas 3

## Deteksi, Analisis, dan Perbaikan Error (PHP & MySQL)

------------------------------------------------------------------------

# 1. Error pada **form-daftar.php**

### **1.1 Error: Penulisan DOCTYPE salah**

-   **Pesan Error**: Tidak muncul sebagai error langsung, tetapi membuat
    browser tidak mengenali HTML5 secara benar.

-   **Jenis Error**: *Syntax / Standard Compliance*

-   **Letak**: Baris pertama: `<DOCTYPE >`

-   **Penyebab**: Penulisan format tidak sesuai standar HTML5.

-   **Perbaikan**:

    ``` html
    <!DOCTYPE html>
    ```

------------------------------------------------------------------------

# 2. Error pada **proses-pendaftaran-2.php**

### **2.1 Error: Variabel tidak menggunakan tanda `$`**

-   **Pesan Error**: *Undefined variable 'sekolah'*

-   **Jenis Error**: *Syntax Error*

-   **Letak**:

    ``` php
    sekolah = $_POST['sekolah_asal'];
    ```

-   **Penyebab**: Tidak memakai tanda `$`.

-   **Perbaikan**:

    ``` php
    $sekolah = $_POST['sekolah_asal'];
    ```

------------------------------------------------------------------------

### **2.2 Error: Salah penulisan kata `VALUE`**

-   **Pesan Error**: *You have an error in your SQL syntax*

-   **Jenis Error**: *Syntax SQL*

-   **Letak**:

    ``` php
    VALUE ('$nama', '$alamat', '$jk', '$agama', '$sekolah')
    ```

-   **Penyebab**: Keyword SQL yang benar adalah **VALUES**.

-   **Perbaikan**:

    ``` php
    VALUES ('$nama', '$alamat', '$jk', '$agama', '$sekolah')
    ```

------------------------------------------------------------------------

### **2.3 Error: Rentan terhadap SQL Injection**

-   **Pesan Error**: Tidak muncul langsung, tetapi berbahaya.

-   **Jenis Error**: *Security Vulnerability*

-   **Letak**: Query masih menggunakan interpolasi string biasa.

-   **Penyebab**: Query tidak memakai prepared statement.

-   **Perbaikan**:\
    Mengganti dengan:

    ``` php
    $stmt = mysqli_prepare($db, $sql);
    mysqli_stmt_bind_param($stmt, "sssss", $nama, $alamat, $jk, $agama, $sekolah);
    ```

------------------------------------------------------------------------

### **2.4 Error: Potensi redirect gagal**

-   **Jenis Error**: *Typo*
-   **Letak**: Komentar tertulis `indek.ph`
-   **Perbaikan**: Tidak memengaruhi aplikasi, hanya dirapikan.

------------------------------------------------------------------------

# 3. Error pada **koneksi.php**

### **3.1 Tidak ada error fatal**

Namun ada beberapa catatan:

#### Catatan 1: Password default XAMPP biasanya kosong

-   Jika mahasiswa tidak mengubah password MySQL, maka:

    ``` php
    $password = "";
    ```

#### Catatan 2: Tidak memakai metode koneksi modern

-   `mysqli_connect()` masih valid, tidak wajib diubah.

------------------------------------------------------------------------

# 4. Ringkasan Perbaikan

  ------------------------------------------------------------------------------
  File                       Error                     Status
  -------------------------- ------------------------- -------------------------
  form-daftar.php            DOCTYPE salah             ✔ Diperbaiki

  proses-pendaftaran-2.php   Variabel tanpa `$`        ✔ Diperbaiki

  proses-pendaftaran-2.php   VALUE → VALUES            ✔ Diperbaiki

  proses-pendaftaran-2.php   Tidak memakai prepared    ✔ Diperbaiki
                             statement                 

  koneksi.php                Tidak ada error           ✔ Aman
  ------------------------------------------------------------------------------

------------------------------------------------------------------------

# 5. Kesimpulan

Semua error telah diperbaiki dan kode telah ditingkatkan menggunakan: -
Prepared Statement (SQL Injection Prevention) - Penulisan HTML yang
benar - Penanganan variabel dan SQL yang tepat

Aplikasi sekarang: ✔ Bisa menyimpan data\
✔ Lebih aman\
✔ Sesuai best practices

------------------------------------------------------------------------

*Dokumentasi selesai.*
