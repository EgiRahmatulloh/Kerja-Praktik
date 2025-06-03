<?php
// Evaluasi konten template dengan data yang disediakan
$renderedContent = $content;

// Ganti semua variabel dengan nilai sebenarnya
foreach ((array)$data as $key => $value) {
    $renderedContent = str_replace("{{ $".$key." }}", $value, $renderedContent);
    $renderedContent = str_replace("{{$".$key."}}", $value, $renderedContent);
}

// Tampilkan konten yang sudah dirender
echo $renderedContent;
?>
