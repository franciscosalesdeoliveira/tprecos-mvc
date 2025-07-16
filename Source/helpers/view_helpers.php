<?php
// helpers/view_helpers.php
// Adicione essas funções em um arquivo de helpers ou no início da sua view

/**
 * Função para obter URL da imagem da propaganda
 */
function getImagemUrl($propaganda, $uploadDir = 'uploads/propagandas/')
{
    $tipoImagem = $propaganda['tipo_imagem'] ?? 'local';

    if ($tipoImagem == 'url') {
        $url = $propaganda['imagem'];
        // Converter URL do Google Drive para visualização direta
        if (strpos($url, 'drive.google.com/file/d/') !== false) {
            preg_match('/\/d\/([^\/]*)/', $url, $matches);
            if (isset($matches[1])) {
                return "https://drive.google.com/uc?export=view&id=" . $matches[1];
            }
        }
        return $url;
    } else {
        return $uploadDir . $propaganda['imagem'];
    }
}

/**
 * Função para verificar se imagem existe
 */
function imagemExiste($propaganda, $uploadDir = 'uploads/propagandas/')
{
    $tipoImagem = $propaganda['tipo_imagem'] ?? 'local';

    if ($tipoImagem == 'url') {
        return !empty($propaganda['imagem']);
    } else {
        return !empty($propaganda['imagem']) && file_exists($uploadDir . $propaganda['imagem']);
    }
}
