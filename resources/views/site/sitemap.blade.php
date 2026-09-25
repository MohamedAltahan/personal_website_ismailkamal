{!! '<?xml version="1.0" encoding="UTF-8"?>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml">
@foreach ($urls as $entry)
@foreach ($entry['alternates'] as $loc)
    <url>
        <loc>{{ $loc }}</loc>
@foreach ($entry['alternates'] as $lang => $href)
        <xhtml:link rel="alternate" hreflang="{{ $lang }}" href="{{ $href }}"/>
@endforeach
@if ($entry['updated'])
        <lastmod>{{ $entry['updated'] }}</lastmod>
@endif
    </url>
@endforeach
@endforeach
</urlset>
