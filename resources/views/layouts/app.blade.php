<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('judul', 'Yayasan Nurul Huda Munjuk')</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --toska: #0fbea8;
            --hijau: #047869;
            --gelap: #123c38;
            --muda: #a7f8ea;
            --garis: #d8f3ef;
            --putih: #ffffff;
            --panel: rgba(255, 255, 255, .94);
            --bayang: 0 18px 45px rgba(0, 71, 76, .12);
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: 'Poppins', Segoe UI, Arial, sans-serif;
            color: #183d39;
            background: #eefdf9;
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        .btn {
            border: 0;
            border-radius: 8px;
            background: linear-gradient(135deg, var(--hijau), var(--toska));
            color: white;
            padding: 10px 14px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: transform .2s ease, box-shadow .2s ease, background .2s ease;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 26px rgba(0, 71, 76, .18);
        }

        .btn.alt {
            background: #fff;
            color: var(--hijau);
            border: 1px solid var(--garis);
        }

        .btn.danger {
            background: linear-gradient(135deg, #dc3545, #f05b68);
        }

        .btn.utama {
            border: 0;
            border-radius: 20px;
            background: linear-gradient(135deg, #ff7a00, #ed9c00);
            color: var(--putih);
            padding: 10px 14px;
            font-weight: 700;
            cursor: pointer;
        }

        input,
        select,
        textarea {
            width: 100%;
            border: 1px solid var(--garis);
            border-radius: 8px;
            padding: 10px;
            background: #fbfffe;
            transition: border-color .2s ease, box-shadow .2s ease;
        }

        input:focus,
        select:focus,
        textarea:focus {
            outline: 0;
            border-color: var(--toska);
            box-shadow: 0 0 0 4px rgba(15, 190, 168, .14);
        }

        textarea {
            min-height: 90px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 10px 28px rgba(15, 190, 168, .08);
            font-size: 14px;
        }

        th,
        td {
            padding: 12px;
            border-bottom: 1px solid var(--garis);
            text-align: left;
            vertical-align: top;
        }

        th {
            background: #d9fff6;
            color: var(--gelap);
        }

        .shell {
            display: flex;
            min-height: 100vh;
            background:
                linear-gradient(135deg, rgba(0, 121, 121, .08), rgba(15, 190, 168, .06)),
                #f6fffd;
        }

        .side {
            width: 270px;
            background:
                linear-gradient(135deg, rgba(0, 121, 121, .90), rgba(15, 190, 168, .0)),
                url('{{ asset('images/bg-utama.png') }}') center/cover no-repeat;
            color: white;
            padding: 24px 18px;
            position: sticky;
            top: 0;
            height: 100vh;
            overflow-y: auto;
            overscroll-behavior: contain;
            box-shadow: 18px 0 50px rgba(0, 71, 76, .18);
            transition: width .2s ease;
        }

        .side::-webkit-scrollbar {
            width: 8px;
        }

        .side::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, .32);
            border-radius: 999px;
        }

        .side-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
        }

        .brand {
            font-size: 35px;
            font-weight: 900;
            line-height: 1.25;
        }

        .brand-subtitle {
            margin: 0;
            color: rgba(255, 255, 255, .9);
            font-size: 13px;
            font-weight: 600;
            line-height: 1.35;
        }

        .role-badge {
            display: inline-flex;
            width: fit-content;
            margin: 14px 0 24px;
            padding: 7px 11px;
            border: 1px solid rgba(255, 255, 255, .28);
            border-radius: 999px;
            background: rgba(255, 255, 255, .13);
            color: rgba(255, 255, 255, .92);
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .4px;
        }

        .menu-toggle {
            display: inline-grid;
            place-items: center;
            flex: 0 0 38px;
            width: 38px;
            height: 38px;
            border: 1px solid rgba(255, 255, 255, .28);
            border-radius: 8px;
            background: rgba(255, 255, 255, .12);
            color: white;
            font-size: 24px;
            line-height: 1;
            cursor: pointer;
        }

        .menu-toggle:hover {
            background: rgba(255, 255, 255, .2);
        }

        .menu a {
            display: block;
            padding: 11px 12px;
            border-radius: 8px;
            margin: 5px 0;
            color: #eafffb;
            font-weight: 300;
            transition: background .2s ease, color .2s ease, transform .2s ease;
        }

        .menu a:hover,
        .menu a.active {
            background: rgba(255, 255, 255, .18);
            color: white;
            transform: translateX(4px);
        }

        .menu-group {
            margin: 7px 0;
            border: 1px solid rgba(255, 255, 255, .14);
            border-radius: 8px;
            background: rgba(255, 255, 255, .07);
            overflow: hidden;
        }

        .menu-group summary {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            padding: 11px 12px;
            color: #eafffb;
            font-weight: 400;
            cursor: pointer;
            list-style: none;
            user-select: none;
            transition: background .2s ease, color .2s ease;
        }

        .menu-group summary::-webkit-details-marker {
            display: none;
        }

        .menu-group summary::after {
            content: "+";
            display: inline-grid;
            place-items: center;
            flex: 0 0 22px;
            width: 22px;
            height: 22px;
            border-radius: 999px;
            background: rgba(255, 255, 255, .14);
            font-size: 16px;
            line-height: 1;
        }

        .menu-group[open] summary {
            background: rgba(255, 255, 255, .14);
            color: white;
        }

        .menu-group[open] summary::after {
            content: "-";
        }

        .submenu {
            padding: 5px 8px 8px;
        }

        .submenu a {
            margin: 3px 0;
            padding: 9px 10px 9px 18px;
            border-radius: 7px;
            font-size: 13px;
            font-weight: 400;
        }

        .menu-collapsed .side {
            width: 86px;
        }

        .menu-collapsed .side-head {
            justify-content: center;
        }

        .menu-collapsed .brand-wrap,
        .menu-collapsed .role-badge,
        .menu-collapsed .menu {
            display: none;
        }

        .content {
            flex: 1;
            padding: 28px;
            min-width: 0;
        }

        .top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 18px;
            margin-bottom: 22px;
            padding: 18px;
            border: 1px solid rgba(216, 243, 239, .9);
            border-radius: 8px;
            background: var(--panel);
            box-shadow: var(--bayang);
        }

        .top h1 {
            color: var(--gelap);
            font-size: clamp(24px, 3vw, 34px);
            line-height: 1.2;
        }

        .card {
            background: var(--panel);
            border: 1px solid var(--garis);
            border-radius: 8px;
            padding: 18px;
            margin-bottom: 18px;
            box-shadow: var(--bayang);
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
            gap: 14px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 12px;
        }

        .alert {
            padding: 12px;
            border-radius: 8px;
            border: 1px solid #bff7ec;
            background: #d9fff6;
            color: #086456;
            margin-bottom: 16px;
            box-shadow: 0 10px 28px rgba(15, 190, 168, .08);
        }

        .muted {
            color: #66827f;
        }

        .thumb {
            width: 92px;
            height: 62px;
            object-fit: cover;
            border-radius: 8px;
            background: var(--muda);
        }

        @media (max-width: 800px) {
            .shell {
                display: block;
            }

            .side {
                position: relative;
                width: auto;
                height: auto;
                padding: 16px 18px;
                box-shadow: 0 12px 32px rgba(0, 71, 76, .16);
            }

            .side-head {
                align-items: center;
            }

            .brand {
                margin-bottom: 0;
                font-size: 18px;
            }

            .brand-subtitle {
                font-size: 11px;
            }

            .role-badge {
                margin: 12px 0 0;
            }

            .menu {
                margin-top: 16px;
            }

            .menu-collapsed .side {
                width: auto;
            }

            .menu-collapsed .side-head {
                justify-content: space-between;
            }

            .menu-collapsed .brand-wrap {
                display: block;
            }

            .menu-collapsed .role-badge {
                display: none;
            }

            .content {
                padding: 18px;
            }

            .top {
                align-items: flex-start;
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    @yield('body')
</body>
</html>
