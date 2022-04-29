<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>No Antrian-{{$antrian->no_antrian}}</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
</head>
<body>
    <p class="text-center">Pengadilan Agama Tegal</p>
    <hr>
    <p class="text-center">No. Antrian</p>
    <p class="text-center" style="font-size:14px">
        {{$antrian->no_antrian}}
    </p>
    @if($antrian->keperluan)
        <p class="text-center">Keperluan</p>
        <p class="text-center" v-if="cetak.keperluan">
            <strong>{{$antrian->keperluan->nama}}</strong>
        </p>
    @endif
    <hr>
    <p class="text-center">Terima Kasih</p>

    <style>
        .text-center{
            text-align:center;
        }
        p{
            font-size:10px;
            line-height:12px;
        }
    </style>

    <script type="text/javascript">
        window.print();
        window.onfocus=function(){ window.close();}
    </script>
</body>
</html>