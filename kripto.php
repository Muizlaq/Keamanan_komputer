<!DOCTYPE html>
<html>
<head>
    <title>KRIPTOGRAFI</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        h1, h2 {
            text-align: center;
        }
        fieldset {
            margin-bottom: 20px;
            padding: 10px;
            border: 2px solid #ccc;
        }
        textarea, input[type="text"], select {
            width: 100%;
            padding: 10px;
            margin: 5px 0;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #45a049;
        }
        table {
            width: 100%;
        }
        td {
            padding: 8px;
        }
        .result {
            height: 200px;
            border: 1px solid #ddd;
            padding: 10px;
            background-color: #f9f9f9;
        }
    </style>
</head>
<body>

<h1>TUGAS KRIPTOGRAFI</h1>
<h2>MUHAMMAD IZZUL HAQ BAHALIM</h2>
<h2>211011401577</h2>

<form action="" method="post">
    <fieldset>
        <legend>RC-4</legend>
        <div>
            <table>
                <tr>
                    <td>Pesan : </td>
                    <td><textarea name="pesan" cols="70" rows="4" required="required"></textarea></td>
                </tr>
                <tr>
                    <td>Kunci : </td>
                    <td><input type="text" name="kunci" required="required"></td>
                </tr>
                <tr>
                    <td>Proses : </td>
                    <td><select name="proses">
                            <option value="E">Enkripsi</option>
                            <option value="D">Dekripsi</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td><input type="submit" name="submit" value="Lakukan !"></td>
                </tr>
            </table>
        </div>
    </fieldset>
</form>

<?php 
if(isset($_POST["submit"])) {
    $pesan = $_POST["pesan"];
    $kunci = $_POST["kunci"];
    $proses = $_POST["proses"];

    $obj = new KripRC4;
    $obj->setKunci($kunci);

    if($proses == "E") {
        $obj->EDkripsi($pesan, $proses);
    } else {
        $pesan = $obj->ubahPesan($pesan);
        $obj->pesanASCI(explode(" ", $pesan));
        $obj->EDkripsi($pesan, $proses);
    }
}

class KripRC4 {
    private $kunci;
    private $S;
    private $K;
    private $H;
    private $pesanAsli;

    public function pesanASCI($n) {
        $this->pesanAsli = $n;
    }

    public function setKunci($n) {
        $this->kunci = $n;
    }

    public function getKunci() {
        return $this->kunci;
    }

    public function ubahPesan($n) {
        $h = explode(" ", $n);
        $hasil = "";
        foreach ($h as $ascii) {
            $hasil .= chr($ascii);
        }
        return $hasil;
    }

    public function iniArrayS() {
        for($i = 0; $i < 256; $i++) {
            $S[$i] = $i;
        }
        $this->S = $S;
    }

    public function iniArrayK() {        
        $key = $this->getKunci();
        for($i = 0; $i < 256; $i++) {
            $K[$i] = ord($key[$i % strlen($key)]);
        }
        $this->K = $K;
    }

    public function acakSBox() {
        $i = 0;
        $j = 0;
        $S = $this->S;
        $K = $this->K;

        for($i = 0; $i < 256; $i++) {
            $j = ($j + $S[$i] + $K[$i]) % 256;
            $temp = $S[$i];
            $S[$i] = $S[$j];
            $S[$j] = $temp;
        }
        $this->S = $S;
    }

    public function pseudoRandomByte($pesan) {
        $S = $this->S;
        $i = 0; 
        $j = 0;
        $Key = [];

        for($p = 0; $p < strlen($pesan); $p++) {
            $i = ($i + 1) % 256;
            $j = ($j + $S[$i]) % 256;
            $temp = $S[$i];
            $S[$i] = $S[$j];
            $S[$j] = $temp;

            $t = ($S[$i] + $S[$j]) % 256;
            $Key[] = $S[$t];
        }
        return $Key;
    }

    public function getHasil($n) {
        $arrHasil = [];
        foreach ($n as $ascii) {
            $arrHasil[] = chr($ascii);
        }
        return $arrHasil;
    }

    public function ubahBinner($n) {
        if (!is_int($n)) {
            $n = ord($n);
        }
        $n = decbin($n);

        return str_pad(substr($n, -8), 8, "0", STR_PAD_LEFT);
    }

    public function hasilXorBinner($p, $k) {
        $arrHasil = [];
        for($i = 0; $i < strlen($p); $i++) {
            $arrHasil[] = $p[$i] == $k[$i] ? "0" : "1";
        }
        return bindec(implode($arrHasil));
    }

    public function prosesXOR($pesan, $kunci, $status) {
        $arrPesan = [];
        $arrHasil = [];

        if ($status == "E") {
            for ($i = 0; $i < strlen($pesan); $i++) {
                $arrPesan[$i] = ord($pesan[$i]);
            }    
        } else {
            $arrPesan = $this->pesanAsli;
        }

        for ($i = 0; $i < count($arrPesan); $i++) {
            $p = $this->ubahBinner($arrPesan[$i]);
            $k = $this->ubahBinner($kunci[$i]);
            $h = $this->hasilXorBinner($p, $k);
            $arrHasil[$i] = $h;
        }

        $hasil = $this->getHasil($arrHasil);
        $this->H = $arrHasil;
        return $hasil;
    }

    public function cetakHasil($hasil) {
        $hasilStr = implode($hasil);
        $asciiStr = implode(" ", $this->H);
        ?>
        <fieldset>
            <legend>Hasil : </legend>
            <div class="result"><?= htmlspecialchars($hasilStr) ?></div>
        </fieldset>
        <fieldset>
            <div style="padding : 10px 0"><?= htmlspecialchars($asciiStr) ?></div>
        </fieldset>
        <?php
    }

    public function EDkripsi($n, $status) {
        $this->iniArrayS();
        $this->iniArrayK();
        $this->acakSBox();

        $key_prb = $this->pseudoRandomByte($n);
        $hasil = $this->prosesXOR($n, $key_prb, $status);

        $this->cetakHasil($hasil);
    }
}
?>

</body>
</html>
