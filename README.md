<!DOCTYPE html>
<html>
<body>

<h1>Endpoint Storage API Documentation</h1>

<b>DEMO URL:</b><br/>
https://larapi-denyocrworld.vercel.app<br><br>

<p>API ini memungkinkan Anda mengelola data pada berbagai endpoint dengan fitur penyimpanan yang dapat diatur menggunakan konsep <em>storage</em> yang dapat disesuaikan.</p>

<h2>Endpoint dan Penyimpanan</h2>

<p>
    <strong>{storage}</strong>: Komponen yang mendefinisikan penyimpanan unik untuk setiap endpoint.
    Anda dapat menggantikan nilai ini dengan apa pun yang sesuai dengan kebutuhan.
    Misalnya, Anda bisa menggunakan lingkungan (prod, dev, staging) atau nama bisnis/proyek yang spesifik.
</p>

<h2>Contoh Penggunaan</h2>

<h3>1. Mengambil Data</h3>

<p><strong>Request:</strong></p>
<pre><code>GET /{storage}/{endpoint}?page=1&amp;per_page=10</code></pre>

<p><strong>Response:</strong></p>
<pre><code>{
    "data": [...],
    "meta": {
        "total": 100,
        "current_page": 1,
        "per_page": 10,
        "total_pages": 10
    }
}</code></pre>

<h3>2. Menambahkan Data Baru</h3>

<p><strong>Request:</strong></p>
<pre><code>POST /{storage}/{endpoint}
Content-Type: application/json

{
    "name": "John Doe",
    "email": "johndoe@example.com"
}</code></pre>

<p><strong>Response:</strong></p>
<pre><code>{
    "id": 101,
    "created_at": "2023-08-29T12:34:56",
    "date": "Sunday, 29 August 2023",
    "time": "12:34:56",
    "name": "John Doe",
    "email": "johndoe@example.com"
}</code></pre>

<h3>3. Mengubah Data</h3>

<p><strong>Request:</strong></p>
<pre><code>PUT /{storage}/{endpoint}/{id}
Content-Type: application/json

{
    "email": "newemail@example.com"
}</code></pre>

<p><strong>Response:</strong></p>
<pre><code>{
    "id": 101,
    "created_at": "2023-08-29T12:34:56",
    "date": "Sunday, 29 August 2023",
    "time": "12:34:56",
    "name": "John Doe",
    "email": "newemail@example.com"
}</code></pre>

<h3>4. Menghapus Data</h3>

<p><strong>Request:</strong></p>
<pre><code>DELETE /{storage}/{endpoint}/{id}</code></pre>

<p><strong>Response:</strong></p>
<pre><code>{
    "id": 101,
    "created_at": "2023-08-29T12:34:56",
    "date": "Sunday, 29 August 2023",
    "time": "12:34:56",
    "name": "John Doe",
    "email": "newemail@example.com"
}</code></pre>

<h3>5. Menghapus Semua Data</h3>

<p><strong>Request:</strong></p>
<pre><code>DELETE /{storage}/{endpoint}/action/delete-all</code></pre>

<p><strong>Response:</strong></p>
<pre><code>{
    "message": "All data deleted"
}</code></pre>

<h2>Penggunaan {storage}</h2>

<p>Anda dapat mengganti nilai <strong>{storage}</strong> dengan apapun yang sesuai dengan kebutuhan Anda. Ini memungkinkan Anda untuk memiliki penyimpanan yang terpisah untuk setiap endpoint, meskipun endpoint-nya sama. Contohnya, jika Anda ingin menyimpan data pada penyimpanan terpisah berdasarkan lingkungan (prod, dev, staging), Anda dapat mengganti <strong>{storage}</strong> dengan nilai <em>prod</em>, <em>dev</em>, atau <em>staging</em> sesuai lingkungan yang digunakan.</p>

</body>
</html>
