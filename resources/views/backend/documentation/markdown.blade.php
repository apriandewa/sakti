@extends('backend.main.index')
@push('title', $title)
@section('content')
<div class="content-wrapper">
    <div class="container-full">
        <div class="content-header">
            <div class="d-flex align-items-center">
                <div class="me-auto">
                    <h3 class="page-title">{{ $title }}</h3>
                    <div class="d-inline-block align-items-center">
                        <nav>
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="mdi mdi-home-outline"></i></a></li>
                                <li class="breadcrumb-item" aria-current="page">Documentation</li>
                                <li class="breadcrumb-item active" aria-current="page">{{ $title }}</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="row">
                <div class="col-12">
                    <div class="box">
                        <div class="box-body" style="background-color: #fff; padding: 30px; border-radius: 8px;">
                            <div class="markdown-body" style="color: #333; line-height: 1.6; font-size: 16px;">
                                {!! $html !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
@endsection

@push('css')
<style>
    /* Basic markdown styling for readability */
    .markdown-body h1, .markdown-body h2, .markdown-body h3, .markdown-body h4, .markdown-body h5, .markdown-body h6 {
        margin-top: 24px;
        margin-bottom: 16px;
        font-weight: 600;
        line-height: 1.25;
        color: #24292e;
    }
    .markdown-body h1 { font-size: 2em; border-bottom: 1px solid #eaecef; padding-bottom: .3em; }
    .markdown-body h2 { font-size: 1.5em; border-bottom: 1px solid #eaecef; padding-bottom: .3em; }
    .markdown-body h3 { font-size: 1.25em; }
    .markdown-body p, .markdown-body blockquote, .markdown-body ul, .markdown-body ol, .markdown-body dl, .markdown-body table, .markdown-body pre, .markdown-body details {
        margin-top: 0;
        margin-bottom: 16px;
    }
    .markdown-body blockquote {
        padding: 0 1em;
        color: #6a737d;
        border-left: .25em solid #dfe2e5;
        background: #f9f9f9;
        margin: 16px 0;
        padding: 12px;
        border-radius: 4px;
    }
    .markdown-body .alert-note { border-left-color: #0969da; background: #ddf4ff; color: #0969da; }
    .markdown-body .alert-important { border-left-color: #8250df; background: #f3e8ff; color: #8250df; }
    .markdown-body .alert-tip { border-left-color: #1a7f37; background: #dafbe1; color: #1a7f37; }
    .markdown-body .alert-title { font-weight: bold; margin-bottom: 8px; display: flex; align-items: center; gap: 6px; font-size: 1.1em; }
    .markdown-body .mermaid { display: flex; justify-content: center; margin: 20px 0; background: #f8f9fa; padding: 15px; border-radius: 8px; border: 1px solid #e9ecef; }
    .markdown-body ul, .markdown-body ol { padding-left: 2em; }
    .markdown-body table { border-spacing: 0; border-collapse: collapse; display: block; width: 100%; overflow: auto; }
    .markdown-body table th, .markdown-body table td { padding: 6px 13px; border: 1px solid #dfe2e5; }
    .markdown-body table tr { background-color: #fff; border-top: 1px solid #c6cbd1; }
    .markdown-body table tr:nth-child(2n) { background-color: #f6f8fa; }
    .markdown-body code {
        padding: .2em .4em;
        margin: 0;
        font-size: 85%;
        background-color: rgba(27,31,35,.05);
        border-radius: 3px;
        font-family: SFMono-Regular,Consolas,Liberation Mono,Menlo,monospace;
    }
    .markdown-body pre {
        padding: 16px;
        overflow: auto;
        font-size: 85%;
        line-height: 1.45;
        background-color: #f6f8fa;
        border-radius: 3px;
    }
    .markdown-body pre code { background-color: transparent; padding: 0; }
</style>
@endpush

@push('js')
<script src="https://cdn.jsdelivr.net/npm/mermaid@9.4.3/dist/mermaid.min.js"></script>
<script>
    mermaid.initialize({ startOnLoad: false, theme: 'default' });
    
    $(document).ready(function() {
        // Find all pre > code.language-mermaid and convert to div.mermaid
        $('.markdown-body pre code.language-mermaid').each(function() {
            var content = $(this).text();
            var $div = $('<div class="mermaid"></div>').text(content);
            $(this).parent().replaceWith($div);
        });
        
        // Run mermaid
        mermaid.init(undefined, '.mermaid');

        // Parse GitHub Alerts
        $('.markdown-body blockquote').each(function() {
            var html = $(this).html();
            // simple hack to replace [!NOTE] wrapped in p tags or raw
            if (html.includes('[!NOTE]')) {
                $(this).addClass('alert-note').html(html.replace(/\[!NOTE\]/g, '<div class="alert-title"><i class="fa fa-info-circle"></i> NOTE</div>'));
            } else if (html.includes('[!IMPORTANT]')) {
                $(this).addClass('alert-important').html(html.replace(/\[!IMPORTANT\]/g, '<div class="alert-title"><i class="fa fa-exclamation-circle"></i> IMPORTANT</div>'));
            } else if (html.includes('[!TIP]')) {
                $(this).addClass('alert-tip').html(html.replace(/\[!TIP\]/g, '<div class="alert-title"><i class="fa fa-lightbulb-o"></i> TIP</div>'));
            }
        });
    });
</script>
@endpush
