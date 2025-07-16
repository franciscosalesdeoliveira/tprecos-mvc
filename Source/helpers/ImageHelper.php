<?php

/**
 * Classe Helper para gerenciamento de imagens
 */
class ImageHelper
{
    /**
     * Gera a URL completa da imagem
     */
    public static function getImageUrl($propaganda)
    {
        $tipoImagem = $propaganda['tipo_imagem'] ?? 'local';
        $caminhoImagem = $propaganda['imagem'] ?? '';

        if (empty($caminhoImagem)) {
            return null;
        }

        if ($tipoImagem === 'url') {
            // Para URLs externas, retornar diretamente
            return $caminhoImagem;
        } else {
            // Para arquivos locais
            $caminhoLimpo = ltrim($caminhoImagem, '/');

            // Gerar URL completa
            $protocolo = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
            $host = $_SERVER['HTTP_HOST'];

            return $protocolo . '://' . $host . '/' . $caminhoLimpo;
        }
    }

    /**
     * Verifica se a imagem existe
     */
    public static function imageExists($propaganda)
    {
        $tipoImagem = $propaganda['tipo_imagem'] ?? 'local';
        $caminhoImagem = $propaganda['imagem'] ?? '';

        if (empty($caminhoImagem)) {
            return false;
        }

        if ($tipoImagem === 'url') {
            // Para URLs externas, verificar se é uma URL válida
            return filter_var($caminhoImagem, FILTER_VALIDATE_URL) !== false;
        } else {
            // Para arquivos locais, verificar se o arquivo existe
            $caminhoLimpo = ltrim($caminhoImagem, '/');
            $caminhoCompleto = $_SERVER['DOCUMENT_ROOT'] . '/' . $caminhoLimpo;

            return file_exists($caminhoCompleto);
        }
    }

    /**
     * Obtém informações detalhadas sobre a imagem
     */
    public static function getImageInfo($propaganda)
    {
        $tipoImagem = $propaganda['tipo_imagem'] ?? 'local';
        $caminhoImagem = $propaganda['imagem'] ?? '';

        $info = [
            'tipo' => $tipoImagem,
            'caminho_original' => $caminhoImagem,
            'url' => self::getImageUrl($propaganda),
            'existe' => self::imageExists($propaganda),
            'erro' => null
        ];

        if ($tipoImagem === 'local' && !empty($caminhoImagem)) {
            $caminhoLimpo = ltrim($caminhoImagem, '/');
            $caminhoCompleto = $_SERVER['DOCUMENT_ROOT'] . '/' . $caminhoLimpo;

            $info['caminho_completo'] = $caminhoCompleto;
            $info['caminho_limpo'] = $caminhoLimpo;

            if (file_exists($caminhoCompleto)) {
                $info['tamanho'] = filesize($caminhoCompleto);
                $info['modificado'] = filemtime($caminhoCompleto);

                // Tentar obter informações da imagem
                $imageInfo = @getimagesize($caminhoCompleto);
                if ($imageInfo) {
                    $info['largura'] = $imageInfo[0];
                    $info['altura'] = $imageInfo[1];
                    $info['tipo_mime'] = $imageInfo['mime'];
                }
            } else {
                $info['erro'] = 'Arquivo não encontrado';
            }
        }

        return $info;
    }

    /**
     * Gera HTML para exibir a imagem
     */
    public static function renderImage($propaganda, $options = [])
    {
        $defaults = [
            'class' => 'img-thumbnail',
            'style' => 'max-height: 60px; max-width: 80px; object-fit: cover;',
            'show_fallback' => true,
            'show_debug' => false
        ];

        $options = array_merge($defaults, $options);

        $imageInfo = self::getImageInfo($propaganda);

        if (!$imageInfo['existe'] || empty($imageInfo['url'])) {
            // Retornar fallback se a imagem não existir
            return self::renderFallback($imageInfo, $options);
        }

        $html = '<div class="position-relative">';

        $html .= sprintf(
            '<img src="%s" class="%s" style="%s" alt="%s" onerror="this.onerror=null;this.style.display=\'none\';this.nextElementSibling.style.display=\'block\';">',
            htmlspecialchars($imageInfo['url']),
            $options['class'],
            $options['style'],
            htmlspecialchars($propaganda['titulo'] ?? '')
        );

        // Fallback para erro de carregamento
        if ($options['show_fallback']) {
            $html .= '<div style="display: none;" class="text-center">
                <span class="badge bg-danger">Erro</span>
                <br>
                <small class="text-muted">Falha ao carregar</small>
            </div>';
        }

        // Link para URL externa
        if ($imageInfo['tipo'] === 'url') {
            $html .= sprintf(
                '<a href="%s" target="_blank" class="position-absolute top-0 end-0 badge bg-info" title="Ver URL original">
                    <i class="bi bi-link-45deg"></i>
                </a>',
                htmlspecialchars($propaganda['imagem'])
            );
        }

        $html .= '</div>';

        return $html;
    }

    /**
     * Renderiza o fallback quando a imagem não existe
     */
    private static function renderFallback($imageInfo, $options)
    {
        $html = '<div class="text-center">';
        $html .= '<span class="badge bg-danger">Sem imagem</span><br>';

        if ($imageInfo['tipo'] === 'url') {
            $html .= '<small class="text-muted">URL inválida</small>';
        } else {
            $html .= '<small class="text-muted">Arquivo não encontrado</small>';
        }

        // Informações de debug
        if ($options['show_debug']) {
            $html .= '<br><small class="text-warning" style="font-size: 10px;">';
            $html .= 'Original: ' . htmlspecialchars($imageInfo['caminho_original']) . '<br>';
            $html .= 'URL: ' . htmlspecialchars($imageInfo['url'] ?? 'N/A');
            if ($imageInfo['erro']) {
                $html .= '<br>Erro: ' . htmlspecialchars($imageInfo['erro']);
            }
            $html .= '</small>';
        }

        $html .= '</div>';

        return $html;
    }

    /**
     * Valida e limpa o caminho da imagem antes de salvar
     */
    public static function sanitizePath($path)
    {
        // Remove caracteres perigosos
        $path = preg_replace('/[^a-zA-Z0-9\-_\.\/]/', '', $path);

        // Remove barras duplas
        $path = preg_replace('/\/+/', '/', $path);

        // Remove barra inicial se existir
        $path = ltrim($path, '/');

        return $path;
    }
}
