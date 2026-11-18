{!! '<' . '?xml version="1.0" encoding="UTF-8"?' . '>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc>{{ route('home') }}</loc>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>
    <url>
        <loc>{{ route('books.index') }}</loc>
        <changefreq>daily</changefreq>
        <priority>0.9</priority>
    </url>
    @foreach ($categories as $cat)
        <url>
            <loc>{{ route('categories.show', $cat->slug) }}</loc>
            <lastmod>{{ $cat->updated_at?->toAtomString() ?? now()->toAtomString() }}</lastmod>
            <changefreq>weekly</changefreq>
            <priority>0.8</priority>
        </url>
    @endforeach
    @foreach ($books as $book)
        <url>
            <loc>{{ route('books.show', $book->slug) }}</loc>
            <lastmod>{{ $book->updated_at?->toAtomString() ?? now()->toAtomString() }}</lastmod>
            <changefreq>weekly</changefreq>
            <priority>0.7</priority>
        </url>
    @endforeach
</urlset>
