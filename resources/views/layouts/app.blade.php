<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('judul', 'Yayasan Nurul Huda Munjuk')</title>
    <style>
        :root{--toska:#0fbea8;--hijau:#047869;--gelap:#123c38;--muda:#e8fffb;--garis:#d8f3ef;--putih:#ffffff}
        *{box-sizing:border-box} body{margin:0;font-family:Inter,Segoe UI,Arial,sans-serif;color:#183d39;background:#f5fbfa}
        a{color:inherit;text-decoration:none}.btn{border:0;border-radius:8px;background:var(--toska);color:white;padding:10px 14px;font-weight:700;cursor:pointer}.btn.alt{background:#fff;color:var(--hijau);border:1px solid var(--garis)}.btn.danger{background:#dc3545}
        input,select,textarea{width:100%;border:1px solid var(--garis);border-radius:8px;padding:10px;background:white} textarea{min-height:90px}
        table{width:100%;border-collapse:collapse;background:white;border-radius:8px;overflow:hidden} th,td{padding:12px;border-bottom:1px solid var(--garis);text-align:left;vertical-align:top} th{background:var(--muda);color:var(--gelap)}
        .shell{display:flex;min-height:100vh}.side{width:270px;background:linear-gradient(180deg,var(--hijau),#075c52);color:white;padding:24px 18px;position:sticky;top:0;height:100vh}.brand{font-size:20px;font-weight:900;margin-bottom:24px}.menu a{display:block;padding:11px 12px;border-radius:8px;margin:5px 0;color:#eafffb}.menu a:hover{background:rgba(255,255,255,.14)}.content{flex:1;padding:28px}.top{display:flex;justify-content:space-between;align-items:center;margin-bottom:22px}.card{background:white;border:1px solid var(--garis);border-radius:8px;padding:18px;margin-bottom:18px;box-shadow:0 10px 28px rgba(15,190,168,.08)}.grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(210px,1fr));gap:14px}.form-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:12px}.alert{padding:12px;border-radius:8px;background:#d9fff6;color:#086456;margin-bottom:16px}.muted{color:#66827f}.thumb{width:92px;height:62px;object-fit:cover;border-radius:8px;background:var(--muda)}
        @media(max-width:800px){.shell{display:block}.side{position:relative;width:auto;height:auto}.content{padding:18px}}
    </style>
</head>
<body>
@yield('body')
</body>
</html>
