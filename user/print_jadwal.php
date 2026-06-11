<?php
include 'jadwal.php';

echo "
<style>
@media print {
    /* Sembunyikan sidebar/menu */
    .sidebar, 
    nav, 
    .menu, 
    #sidebar,
    .left-menu,
    [class*='sidebar'],
    [class*='menu'] {
        display: none !important;
    }
    
    /* Sembunyikan tombol-tombol */
    button, 
    .btn, 
    a[href='#'], 
    .no-print {
        display: none !important;
    }
    
    /* Atur layout untuk print */
    body {
        margin: 0;
        padding: 0;
    }
    
    .content, 
    .main-content,
    #content,
    [class*='content'] {
        width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
    }
    
    /* Pastikan tabel terlihat bagus */
    table {
        width: 100%;
        border-collapse: collapse;
    }
    
    th, td {
        border: 1px solid #000;
        padding: 8px;
    }
}
</style>

<script>
window.print();
</script>
";
?>