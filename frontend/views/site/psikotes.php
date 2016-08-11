<?php
//============================================================+
// File name   : example_048.php
// Begin       : 2009-03-20
// Last Update : 2013-05-14
//
// Description : Example 048 for TCPDF class
//               HTML tables and table headers
//
// Author: Nicola Asuni
//
// (c) Copyright:
//               Nicola Asuni
//               Tecnick.com LTD
//               www.tecnick.com
//               info@tecnick.com
//============================================================+

/**
* Creates an example PDF TEST document using TCPDF
* @package com.tecnick.tcpdf
* @abstract TCPDF - Example: HTML tables and table headers
* @author Nicola Asuni
* @since 2009-03-20
*/
// Dummy Variable Data Diri
$nama           = $model->firstname . ' ' . $model->lastname;
$no             = "12323123123123123";
$jabatanlamar   = $model->jabatan_dilamar;

$ttl            = $model->tempat_lahir .', ' . $model->tanggal_lahir;
$tujuan         = $model->tujuan_pemeriksaan;
$pendidikan     = $model->pendidikan_terakhir;
$tgltes         = $model->tanggal_test;
$tempattes      = $model->tempat;
$ttd            = "Drs. Budiman Sanusi, MPsi.";
$himpsi         = "0111891963";

$namaaspek = "GENERAL INTELLIGENCE";
$judul     = "PSIKOGRAM HASIL ASSESSMENT / PEMERIKSAAN PSIKOLOGIS";

$bobot1 = 24;
$bobot2 = 8;
$bobot3 = 8;
$bobot4 = 10;
$bobot5 = 6;
$bobot6 = 8;
$bobot7 = 10;
$bobot8 = 10;
$bobot9 = 10;
$bobot10 = 6;

$total_bobot =  $bobot1 + $bobot2 + $bobot3+ $bobot4+ $bobot5+ $bobot6+ $bobot7+ $bobot8+ $bobot9+ $bobot10;

$total_min = 0;
$total_pribadi = 0;
$total_max = 0;

$min1 = 1 * $bobot1;$min2 = 1 * $bobot2;$min3 = 1 * $bobot3;$min4 = 1 * $bobot4;$min5 = 1 * $bobot5;$min6 = 1 * $bobot6;$min7 = 1 * $bobot7;$min8 = 1 * $bobot8;$min9 = 1 * $bobot9;$min10 = 1 * $bobot10;
$max1 = 7 * $bobot1;$max2 = 7 * $bobot2;$max3 = 7 * $bobot3;$max4 = 7 * $bobot4;$max5 = 7 * $bobot5;$max6 = 7 * $bobot6;$max7 = 7 * $bobot7;$max8 = 7 * $bobot8;$max9 = 7 * $bobot9;$max10 = 7 * $bobot10;

$total_min = $min1 + $min2 + $min3 + $min4 + $min5 + $min6 + $min7 + $min8 + $min9 + $min10;
$total_max = $max1 + $max2 + $max3 + $max4 + $max5 + $max6 + $max7 + $max8 + $max9 + $max10;


$rating1 = $cfit->scaled;
$rating2 = 4;
$rating3 = 4;
$rating4 = 4;
$rating5 = 4;
$rating6 = 4;
$rating7 = 4;
$rating8 = 4;
$rating9 = 4;
$rating10 = 4;

$pribadi1 = $rating1 * $bobot1 ;$pribadi2 = 120;$pribadi3 = 120;$pribadi4 = 120;$pribadi5 = 120;$pribadi6 = 120;$pribadi7 = 120;$pribadi8 = 120;$pribadi9 = 120;$pribadi10 = 120;
$total_pribadi = $pribadi1 + $pribadi2 + $pribadi3 + $pribadi4 + $pribadi5 + $pribadi6 + $pribadi7 + $pribadi8 + $pribadi9 + $pribadi10;
if ($rating1 == 1 ){ $rat11 = "grey";} else { $rat11 = "";}
if ($rating1 == 2 ){ $rat12 = "grey";} else { $rat12 = "";}
if ($rating1 == 3 ){ $rat13 = "grey";} else { $rat13 = "";}
if ($rating1 == 4 ){ $rat14 = "grey";} else { $rat14 = "";}
if ($rating1 == 5 ){ $rat15 = "grey";} else { $rat15 = "";}
if ($rating1 == 6 ){ $rat16 = "grey";} else { $rat16 = "";}
if ($rating1 == 7 ){ $rat17 = "grey";} else { $rat17 = "";}

if ($rating2 == 1 ){ $rat21 = "grey";} else { $rat21 = "";}
if ($rating2 == 2 ){ $rat22 = "grey";} else { $rat22 = "";}
if ($rating2 == 3 ){ $rat23 = "grey";} else { $rat23 = "";}
if ($rating2 == 4 ){ $rat24 = "grey";} else { $rat24 = "";}
if ($rating2 == 5 ){ $rat25 = "grey";} else { $rat25 = "";}
if ($rating2 == 6 ){ $rat26 = "grey";} else { $rat26 = "";}
if ($rating2 == 7 ){ $rat27 = "grey";} else { $rat27 = "";}

if ($rating3 == 1 ){ $rat31 = "grey";} else { $rat31 = "";}
if ($rating3 == 2 ){ $rat32 = "grey";} else { $rat32 = "";}
if ($rating3 == 3 ){ $rat33 = "grey";} else { $rat33 = "";}
if ($rating3 == 4 ){ $rat34 = "grey";} else { $rat34 = "";}
if ($rating3 == 5 ){ $rat35 = "grey";} else { $rat35 = "";}
if ($rating3 == 6 ){ $rat36 = "grey";} else { $rat36 = "";}
if ($rating3 == 7 ){ $rat37 = "grey";} else { $rat37 = "";}

if ($rating4 == 1 ){ $rat41 = "grey";} else { $rat41 = "";}
if ($rating4 == 2 ){ $rat42 = "grey";} else { $rat42 = "";}
if ($rating4 == 3 ){ $rat43 = "grey";} else { $rat43 = "";}
if ($rating4 == 4 ){ $rat44 = "grey";} else { $rat44 = "";}
if ($rating4 == 5 ){ $rat45 = "grey";} else { $rat45 = "";}
if ($rating4 == 6 ){ $rat46 = "grey";} else { $rat46 = "";}
if ($rating4 == 7 ){ $rat47 = "grey";} else { $rat47 = "";}

if ($rating5 == 1 ){ $rat51 = "grey";} else { $rat51 = "";}
if ($rating5 == 2 ){ $rat52 = "grey";} else { $rat52 = "";}
if ($rating5 == 3 ){ $rat53 = "grey";} else { $rat53 = "";}
if ($rating5 == 4 ){ $rat54 = "grey";} else { $rat54 = "";}
if ($rating5 == 5 ){ $rat55 = "grey";} else { $rat55 = "";}
if ($rating5 == 6 ){ $rat56 = "grey";} else { $rat56 = "";}
if ($rating5 == 7 ){ $rat57 = "grey";} else { $rat57 = "";}

if ($rating6 == 1 ){ $rat61 = "grey";} else { $rat61 = "";}
if ($rating6 == 2 ){ $rat62 = "grey";} else { $rat62 = "";}
if ($rating6 == 3 ){ $rat63 = "grey";} else { $rat63 = "";}
if ($rating6 == 4 ){ $rat64 = "grey";} else { $rat64 = "";}
if ($rating6 == 5 ){ $rat65 = "grey";} else { $rat65 = "";}
if ($rating6 == 6 ){ $rat66 = "grey";} else { $rat66 = "";}
if ($rating6 == 7 ){ $rat67 = "grey";} else { $rat67 = "";}

if ($rating7 == 1 ){ $rat71 = "grey";} else { $rat71 = "";}
if ($rating7 == 2 ){ $rat72 = "grey";} else { $rat72 = "";}
if ($rating7 == 3 ){ $rat73 = "grey";} else { $rat73 = "";}
if ($rating7 == 4 ){ $rat74 = "grey";} else { $rat74 = "";}
if ($rating7 == 5 ){ $rat75 = "grey";} else { $rat75 = "";}
if ($rating7 == 6 ){ $rat76 = "grey";} else { $rat76 = "";}
if ($rating7 == 7 ){ $rat77 = "grey";} else { $rat77 = "";}

if ($rating8 == 1 ){ $rat81 = "grey";} else { $rat81 = "";}
if ($rating8 == 2 ){ $rat82 = "grey";} else { $rat82 = "";}
if ($rating8 == 3 ){ $rat83 = "grey";} else { $rat83 = "";}
if ($rating8 == 4 ){ $rat84 = "grey";} else { $rat84 = "";}
if ($rating8 == 5 ){ $rat85 = "grey";} else { $rat85 = "";}
if ($rating8 == 6 ){ $rat86 = "grey";} else { $rat86 = "";}
if ($rating8 == 7 ){ $rat87 = "grey";} else { $rat87 = "";}

if ($rating9 == 1 ){ $rat91 = "grey";} else { $rat91 = "";}
if ($rating9 == 2 ){ $rat92 = "grey";} else { $rat92 = "";}
if ($rating9 == 3 ){ $rat93 = "grey";} else { $rat93 = "";}
if ($rating9 == 4 ){ $rat94 = "grey";} else { $rat94 = "";}
if ($rating9 == 5 ){ $rat95 = "grey";} else { $rat95 = "";}
if ($rating9 == 6 ){ $rat96 = "grey";} else { $rat96 = "";}
if ($rating9 == 7 ){ $rat97 = "grey";} else { $rat97 = "";}

if ($rating10 == 1 ){ $rat101 = "grey";} else { $rat101 = "";}
if ($rating10 == 2 ){ $rat102 = "grey";} else { $rat102 = "";}
if ($rating10 == 3 ){ $rat103 = "grey";} else { $rat103 = "";}
if ($rating10 == 4 ){ $rat104 = "grey";} else { $rat104 = "";}
if ($rating10 == 5 ){ $rat105 = "grey";} else { $rat105 = "";}
if ($rating10 == 6 ){ $rat106 = "grey";} else { $rat106 = "";}
if ($rating10 == 7 ){ $rat107 = "grey";} else { $rat107 = "";}

$namaaspek1 = "<B>GENERAL INTELLIGENCE</B><BR> Kemampuan dalam menangkap, mengolah, mencerna informasi,mengolah, mencerna informasi,mengolah, mencerna informasi, kemudian memakai atau menggunakannya sesuai dengan kebutuhan.";
$namaaspek2 = "<B>ACHIEVEMENT MOTIVATION</B><BR> Keinginan atau kekuatan yang mendorong diri untuk selalu berprestasi tinggi dan tidak cepat merasa puas dengan apa yang sudah dihasilkan.";
$namaaspek3 = "<B>INTERPERSONAL UNDERSTANDING</B><BR> Kepekaan memahami permasalahan dan kondisi orang lain. Serta kemampuan menghadapi orang secara efektif dalam berbagai situasi.";
$namaaspek4 = "<B>STABILITAS EMOSI</B><BR> Kematangan pribadi, mampu mengendalikan emosi, tidak mudah marah serta mampu menyesuaikan emosi dengan situasi.";
$namaaspek5 = "<B>PENGAMBILAN RESIKO</B><BR> Kesiapan diri untuk menghadapai kegagalan dengan tetap menampilkan respon yang positif atas keputusan dan tindakan yang dipilih/diambil.";
$namaaspek6 = "<B>KEPERCAYAAN DIRI</B><BR> Sikap optimis dan rasa percaya diri terhadap seluruh kondisi dan potensi yang dimiliki.";
$namaaspek7 = "<B>INISIATIF</B><BR> Kemampuan individu untuk bertindak melebihi tuntutan tugas untuk meningkatkan hasil serta menghindari masalah serta menemukan kesempatan-2 baru.";
$namaaspek8 = "<B>KERJASAMA</B><BR> Kemampuan bekerja dalam kelompok dan aktif berpartisipasi dalam pencapaian tujuan kelompok.";
$namaaspek9 = "<B>KETEKUNAN</B><BR> Daya tahan menghadapi dan menyelesaikan tugas sampai tuntas dalam waktu relatif lama dengan mencapai hasil yang optimal.";
$namaaspek10 = "<B>KEMANDIRIAN</B><BR> KKemampuan untuk tetap bertahan menampilkan perilaku yang konstruktif/positif, meskipun dalam lingkungan/suasana yang tidak kondusif.";

$rekomendasi = "Memperhatikan seluruh gambaran aspek Psikologi yang dimiliki, dikaitkan dengan kemungkinan keberhasilannya untuk bekerja memikul beban tugas dan tanggung jawab kerja yang lebih besar, maka potensi psikologinya secara umum tergolong :";
// Include the main TCPDF library (search for installation path).
 ob_end_clean();

//require_once('tcpdf_include.php');


// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information

// set header and footer fonts

$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
$pdf->SetPrintHeader(false);
$pdf->SetPrintFooter(false);
// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);


$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
   require_once(dirname(__FILE__).'/lang/eng.php');
   $pdf->setLanguageArray($l);
}


$pdf->AddPage();


$pdf->SetFont('helvetica', '', 9);

// -----------------------------------------------------------------------------

$tbl = <<<EOD
<BR><BR><BR><BR>
<table cellspacing="0" cellpadding="1" border="2">
<tr>
<td bgcolor="#bebebe" colspan="2" align="center"><H1><B>PSIKOGRAM</B></H1></td>
</tr>
<tr>
<td bgcolor="#bebebe" width="80%" align="center"><H1><B>HASIL ASSESSMENT/PEMERIKSAAN PSIKOLOGIS</B></H1></td>
<td bgcolor="black" width="20%" align="center"><H1><B><font color="white">RAHASIA</font></B></H1></td>
</tr>
</table>
<BR>
<BR>
<table cellspacing="0" cellpadding="1" border="2">

    <tr>

       <td>
       <table cellspacing="0" cellpadding="1" border="0">
            <tr>
                <td width="20%">No. Tes</td>
                <td width="3%">:</td>
                <td width="77%">$no</td>
            </tr>
            <tr>
                <td>Nama Lengkap</td>
                <td>:</td>
                <td>$nama</td>
            </tr><tr>
                <td>Tempat / Tgl. Lahir</td>
                <td>:</td>
                <td>$ttl</td>
            </tr><tr>
                <td>Jabatan yang dilamar</td>
                <td>:</td>
                <td>$jabatanlamar</td>
            </tr><tr>
                <td>Pendidikan Terakhir</td>
                <td>:</td>
                <td>$pendidikan</td>
            </tr>
            <tr>
                <td>Tujuan Pemeriksaan</td>
                <td>:</td>
                <td>$tujuan</td>
            </tr>
            <tr>
                <td>Tempat / Tgl. Test</td>
                <td>:</td>
                <td>$tempattes / $tgltes</td>
            </tr>
       </table>
       </td>


    </tr>

</table>
EOD;

$pdf->writeHTML($tbl, true, false, false, false, '');

$pdf->SetFont('helvetica', '', 7);

$tbl = <<<EOD
</BR>

<table>
<tr>
<td width="100%" >

<table cellspacing="0" cellpadding="1" border="1">
    <tr>
        <td  bgcolor="#bebebe" rowspan="2" align="CENTER" width="66%"><h3><B>ASPEK - ASPEK PENILAIAN</B></h3></td>
        <td  bgcolor="#bebebe" rowspan="2"align="center" width="5%"><B>Bobot</B></td>
        <td  bgcolor="#bebebe" rowspan="2" align="center" width="14%"><h3><B>RATING</B></h3></td>
        <td  bgcolor="#bebebe" colspan="3" align="center" width="15%"><h3><B>SKOR</B></h3></td>
    </tr>
    <tr>
        <td  bgcolor="#bebebe" align="center"><B>MIN</B></td>
        <td  bgcolor="#bebebe" align="center"><B>PRIBDI</B></td>
        <td  bgcolor="#bebebe" align="center"><B>MAKS</B></td>
    </tr>
</table>

<table border="1">
    <tr>
        <td  rowspan="4" align="left" width="66%"><h3><B>A. ASPEK KECERDASAN</B></h3></td>
        <td  align="center" width="5%"><h3><B></B></h3></td>
        <td  align="center" width="14%"><h3><B></B></h3></td>
        <td  align="center" width="15%"><h3><B></B></h3></td>
    </tr>
</table>

<table cellspacing="0" cellpadding="1" border="1">
    <tr>
        <td rowspan="3" align="center" width="5%"><BR><BR>1</td>
        <td rowspan="3" align="left" width="61%">$namaaspek1</td>
        <td rowspan="3" align="left" width="5%" align="center"><B><BR>$bobot1</B></td>
        <td bgcolor="#bebebe"  align="left" width="2%" align="center">1</td>
        <td bgcolor="#bebebe"  align="left" width="2%" align="center">2</td>
        <td bgcolor="#bebebe"  align="left" width="2%" align="center">3</td>
        <td bgcolor="#bebebe"  align="left" width="2%" align="center">4</td>
        <td bgcolor="#bebebe"  align="left" width="2%" align="center">5</td>
        <td bgcolor="#bebebe"  align="left" width="2%" align="center">6</td>
        <td bgcolor="#bebebe"  align="left" width="2%" align="center">7</td>
        <td rowspan="3" align="center" width="5%"><BR><BR><B>$min1</B></td>
        <td rowspan="3" align="center" width="5%"><BR><BR><B>$pribadi1</B></td>
        <td rowspan="3" align="center" width="5%"><BR><BR><B>$max1</B></td>
    </tr>
    <tr>
    <td  align="left" width="2%" align="center" bgcolor="$rat11"></td>
        <td  align="left" width="2%" align="center" bgcolor="$rat12"></td>
        <td  align="left" width="2%" align="center" bgcolor="$rat13"></td>
        <td  align="left" width="2%" align="center" bgcolor="$rat14"></td>
        <td  align="left" width="2%" align="center" bgcolor="$rat15"></td>
        <td  align="left" width="2%" align="center" bgcolor="$rat16"></td>
        <td  align="left" width="2%" align="center" bgcolor="$rat17"></td>
    </tr>
    <tr>
    <td  align="left" width="2%" align="center"></td>
        <td  align="left" width="2%" align="center"></td>
        <td  align="left" width="2%" align="center"></td>
        <td  align="left" width="2%" align="center"></td>
        <td  align="left" width="2%" align="center"></td>
        <td  align="left" width="2%" align="center"></td>
        <td  align="left" width="2%" align="center"></td>
    </tr>
</table>

<table cellspacing="0" cellpadding="1" border="1">
    <tr>
        <td rowspan="3" align="center" width="5%"><BR><BR>2</td>
        <td rowspan="3" align="left" width="61%">$namaaspek2</td>
        <td rowspan="3" align="left" width="5%" align="center"><BR><BR><B>$bobot2</B></td>
        <td bgcolor="#bebebe" bgcolor="#bebebe"  align="left" width="2%" align="center">1</td>
        <td bgcolor="#bebebe"  align="left" width="2%" align="center">2</td>
        <td bgcolor="#bebebe"  align="left" width="2%" align="center">3</td>
        <td bgcolor="#bebebe"  align="left" width="2%" align="center">4</td>
        <td bgcolor="#bebebe"  align="left" width="2%" align="center">5</td>
        <td bgcolor="#bebebe"  align="left" width="2%" align="center">6</td>
        <td bgcolor="#bebebe"  align="left" width="2%" align="center">7</td>
        <td rowspan="3" align="center" width="5%"><BR><BR><B>$min2</B></td>
        <td rowspan="3" align="center" width="5%"><BR><BR><B>$pribadi2</B></td>
        <td rowspan="3" align="center" width="5%"><BR><BR><B>$max2</B></td>
    </tr>
    <tr>
    <td  align="left" width="2%" align="center" bgcolor="$rat21"></td>
        <td  align="left" width="2%" align="center" bgcolor="$rat22"></td>
        <td  align="left" width="2%" align="center" bgcolor="$rat23"></td>
        <td  align="left" width="2%" align="center" bgcolor="$rat24"></td>
        <td  align="left" width="2%" align="center" bgcolor="$rat25"></td>
        <td  align="left" width="2%" align="center" bgcolor="$rat26"></td>
        <td  align="left" width="2%" align="center" bgcolor="$rat27"></td>
    </tr>
    <tr>
    <td  align="left" width="2%" align="center"></td>
        <td  align="left" width="2%" align="center"></td>
        <td  align="left" width="2%" align="center"></td>
        <td  align="left" width="2%" align="center"></td>
        <td  align="left" width="2%" align="center"></td>
        <td  align="left" width="2%" align="center"></td>
        <td  align="left" width="2%" align="center"></td>
    </tr>
</table>

<table cellspacing="0" cellpadding="1" border="1">
    <tr>
        <td rowspan="3" align="center" width="5%"><BR><BR>3</td>
        <td rowspan="3" align="left" width="61%">$namaaspek3</td>
        <td rowspan="3" align="left" width="5%" align="center"><BR><BR><B>$bobot3</B></td>
        <td bgcolor="#bebebe"  align="left" width="2%" align="center">1</td>
        <td bgcolor="#bebebe"  align="left" width="2%" align="center">2</td>
        <td bgcolor="#bebebe"  align="left" width="2%" align="center">3</td>
        <td bgcolor="#bebebe"  align="left" width="2%" align="center">4</td>
        <td bgcolor="#bebebe"  align="left" width="2%" align="center">5</td>
        <td bgcolor="#bebebe"  align="left" width="2%" align="center">6</td>
        <td bgcolor="#bebebe"  align="left" width="2%" align="center">7</td>
        <td rowspan="3" align="center" width="5%"><BR><BR><B>$min3</B></td>
        <td rowspan="3" align="center" width="5%"><BR><BR><B>$pribadi3</B></td>
        <td rowspan="3" align="center" width="5%"><BR><BR><B>$max3</B></td>
    </tr>
    <tr>
   <td  align="left" width="2%" align="center" bgcolor="$rat31"></td>
        <td  align="left" width="2%" align="center" bgcolor="$rat32"></td>
        <td  align="left" width="2%" align="center" bgcolor="$rat33"></td>
        <td  align="left" width="2%" align="center" bgcolor="$rat34"></td>
        <td  align="left" width="2%" align="center" bgcolor="$rat35"></td>
        <td  align="left" width="2%" align="center" bgcolor="$rat36"></td>
        <td  align="left" width="2%" align="center" bgcolor="$rat37"></td>
    </tr>
    <tr>
    <td  align="left" width="2%" align="center"></td>
        <td  align="left" width="2%" align="center"></td>
        <td  align="left" width="2%" align="center"></td>
        <td  align="left" width="2%" align="center"></td>
        <td  align="left" width="2%" align="center"></td>
        <td  align="left" width="2%" align="center"></td>
        <td  align="left" width="2%" align="center"></td>
    </tr>
</table>

<table cellspacing="0" cellpadding="1" border="1">
    <tr>
        <td rowspan="3" align="center" width="5%"><BR><BR>4</td>
        <td rowspan="3" align="left" width="61%">$namaaspek4</td>
        <td rowspan="3" align="left" width="5%" align="center"><BR><BR><B>$bobot4</B></td>
        <td bgcolor="#bebebe"  align="left" width="2%" align="center">1</td>
        <td  bgcolor="#bebebe" align="left" width="2%" align="center">2</td>
        <td  bgcolor="#bebebe" align="left" width="2%" align="center">3</td>
        <td  bgcolor="#bebebe" align="left" width="2%" align="center">4</td>
        <td  bgcolor="#bebebe" align="left" width="2%" align="center">5</td>
        <td  bgcolor="#bebebe" align="left" width="2%" align="center">6</td>
        <td  bgcolor="#bebebe" align="left" width="2%" align="center">7</td>
        <td rowspan="3" align="center" width="5%"><BR><BR><B>$min4</B></td>
        <td rowspan="3" align="center" width="5%"><BR><BR><B>$pribadi4</B></td>
        <td rowspan="3" align="center" width="5%"><BR><BR><B>$max4</B></td>
    </tr>
    <tr>
    <td  align="left" width="2%" align="center" bgcolor="$rat41"></td>
        <td  align="left" width="2%" align="center" bgcolor="$rat42"></td>
        <td  align="left" width="2%" align="center" bgcolor="$rat43"></td>
        <td  align="left" width="2%" align="center" bgcolor="$rat44"></td>
        <td  align="left" width="2%" align="center" bgcolor="$rat45"></td>
        <td  align="left" width="2%" align="center" bgcolor="$rat46"></td>
        <td  align="left" width="2%" align="center" bgcolor="$rat47"></td>
    </tr>
    <tr>
    <td  align="left" width="2%" align="center"></td>
        <td  align="left" width="2%" align="center"></td>
        <td  align="left" width="2%" align="center"></td>
        <td  align="left" width="2%" align="center"></td>
        <td  align="left" width="2%" align="center"></td>
        <td  align="left" width="2%" align="center"></td>
        <td  align="left" width="2%" align="center"></td>
    </tr>
</table>

<table cellspacing="0" cellpadding="1" border="1">
    <tr>
        <td rowspan="3" align="center" width="5%"><BR><BR>5</td>
        <td rowspan="3" align="left" width="61%">$namaaspek5</td>
        <td rowspan="3" align="left" width="5%" align="center"><BR><BR><B>$bobot5</B></td>
        <td bgcolor="#bebebe"  align="left" width="2%" align="center">1</td>
        <td bgcolor="#bebebe"  align="left" width="2%" align="center">2</td>
        <td bgcolor="#bebebe"  align="left" width="2%" align="center">3</td>
        <td bgcolor="#bebebe"  align="left" width="2%" align="center">4</td>
        <td bgcolor="#bebebe"  align="left" width="2%" align="center">5</td>
        <td bgcolor="#bebebe"  align="left" width="2%" align="center">6</td>
        <td bgcolor="#bebebe"  align="left" width="2%" align="center">7</td>
        <td rowspan="3" align="center" width="5%"><BR><BR><B>$min5</B></td>
        <td rowspan="3" align="center" width="5%"><BR><BR><B>$pribadi5</B></td>
        <td rowspan="3" align="center" width="5%"><BR><BR><B>$max5</B></td>
    </tr>
    <tr>
    <td  align="left" width="2%" align="center" bgcolor="$rat51"></td>
        <td  align="left" width="2%" align="center" bgcolor="$rat52"></td>
        <td  align="left" width="2%" align="center" bgcolor="$rat53"></td>
        <td  align="left" width="2%" align="center" bgcolor="$rat54"></td>
        <td  align="left" width="2%" align="center" bgcolor="$rat55"></td>
        <td  align="left" width="2%" align="center" bgcolor="$rat56"></td>
        <td  align="left" width="2%" align="center" bgcolor="$rat57"></td>
    </tr>
    <tr>
    <td  align="left" width="2%" align="center"></td>
        <td  align="left" width="2%" align="center"></td>
        <td  align="left" width="2%" align="center"></td>
        <td  align="left" width="2%" align="center"></td>
        <td  align="left" width="2%" align="center"></td>
        <td  align="left" width="2%" align="center"></td>
        <td  align="left" width="2%" align="center"></td>
    </tr>
</table>

<table cellspacing="0" cellpadding="1" border="1">
    <tr>
        <td rowspan="3" align="center" width="5%"><BR><BR>6</td>
        <td rowspan="3" align="left" width="61%">$namaaspek6</td>
        <td rowspan="3" align="left" width="5%" align="center"><BR><BR><B>$bobot6</B></td>
        <td bgcolor="#bebebe"  align="left" width="2%" align="center">1</td>
        <td bgcolor="#bebebe"  align="left" width="2%" align="center">2</td>
        <td bgcolor="#bebebe"  align="left" width="2%" align="center">3</td>
        <td bgcolor="#bebebe"  align="left" width="2%" align="center">4</td>
        <td bgcolor="#bebebe"  align="left" width="2%" align="center">5</td>
        <td bgcolor="#bebebe"  align="left" width="2%" align="center">6</td>
        <td bgcolor="#bebebe"  align="left" width="2%" align="center">7</td>
        <td rowspan="3" align="center" width="5%"><BR><BR><B>$min6</B></td>
        <td rowspan="3" align="center" width="5%"><BR><BR><B>$pribadi6</B></td>
        <td rowspan="3" align="center" width="5%"><BR><BR><B>$max6</B></td>
    </tr>
    <tr>
    <td  align="left" width="2%" align="center" bgcolor="$rat61"></td>
        <td  align="left" width="2%" align="center" bgcolor="$rat62"></td>
        <td  align="left" width="2%" align="center" bgcolor="$rat63"></td>
        <td  align="left" width="2%" align="center" bgcolor="$rat64"></td>
        <td  align="left" width="2%" align="center" bgcolor="$rat65"></td>
        <td  align="left" width="2%" align="center" bgcolor="$rat66"></td>
        <td  align="left" width="2%" align="center" bgcolor="$rat67"></td>
    </tr>
    <tr>
    <td  align="left" width="2%" align="center"></td>
        <td  align="left" width="2%" align="center"></td>
        <td  align="left" width="2%" align="center"></td>
        <td  align="left" width="2%" align="center"></td>
        <td  align="left" width="2%" align="center"></td>
        <td  align="left" width="2%" align="center"></td>
        <td  align="left" width="2%" align="center"></td>
    </tr>
</table>

<table cellspacing="0" cellpadding="1" border="1">
    <tr>
        <td rowspan="3" align="center" width="5%"><BR><BR>7</td>
        <td rowspan="3" align="left" width="61%">$namaaspek7</td>
        <td rowspan="3" align="left" width="5%" align="center"><BR><BR><B>$bobot7</B></td>
        <td bgcolor="#bebebe"  align="left" width="2%" align="center">1</td>
        <td bgcolor="#bebebe"  align="left" width="2%" align="center">2</td>
        <td bgcolor="#bebebe"  align="left" width="2%" align="center">3</td>
        <td bgcolor="#bebebe"  align="left" width="2%" align="center">4</td>
        <td bgcolor="#bebebe"  align="left" width="2%" align="center">5</td>
        <td bgcolor="#bebebe"  align="left" width="2%" align="center">6</td>
        <td bgcolor="#bebebe"  align="left" width="2%" align="center">7</td>
        <td rowspan="3" align="center" width="5%"><BR><BR><B>$min7</B></td>
        <td rowspan="3" align="center" width="5%"><BR><BR><B>$pribadi7</B></td>
        <td rowspan="3" align="center" width="5%"><BR><BR><B>$max7</B></td>
    </tr>
    <tr>
    <td  align="left" width="2%" align="center" bgcolor="$rat71"></td>
        <td  align="left" width="2%" align="center" bgcolor="$rat72"></td>
        <td  align="left" width="2%" align="center" bgcolor="$rat73"></td>
        <td  align="left" width="2%" align="center" bgcolor="$rat74"></td>
        <td  align="left" width="2%" align="center" bgcolor="$rat75"></td>
        <td  align="left" width="2%" align="center" bgcolor="$rat76"></td>
        <td  align="left" width="2%" align="center" bgcolor="$rat77"></td>
    </tr>
    <tr>
    <td  align="left" width="2%" align="center"></td>
        <td  align="left" width="2%" align="center"></td>
        <td  align="left" width="2%" align="center"></td>
        <td  align="left" width="2%" align="center"></td>
        <td  align="left" width="2%" align="center"></td>
        <td  align="left" width="2%" align="center"></td>
        <td  align="left" width="2%" align="center"></td>
    </tr>
</table>

<table cellspacing="0" cellpadding="1" border="1">
    <tr>
        <td rowspan="3" align="center" width="5%"><BR><BR>8</td>
        <td rowspan="3" align="left" width="61%">$namaaspek8</td>
        <td rowspan="3" align="left" width="5%" align="center"><BR><BR><B>$bobot8</B></td>
        <td bgcolor="#bebebe"  align="left" width="2%" align="center">1</td>
        <td bgcolor="#bebebe"  align="left" width="2%" align="center">2</td>
        <td bgcolor="#bebebe"  align="left" width="2%" align="center">3</td>
        <td bgcolor="#bebebe"  align="left" width="2%" align="center">4</td>
        <td bgcolor="#bebebe"  align="left" width="2%" align="center">5</td>
        <td bgcolor="#bebebe"  align="left" width="2%" align="center">6</td>
        <td bgcolor="#bebebe"  align="left" width="2%" align="center">7</td>
        <td rowspan="3" align="center" width="5%"><BR><BR><B>$min8</B></td>
        <td rowspan="3" align="center" width="5%"><BR><BR><B>$pribadi8</B></td>
        <td rowspan="3" align="center" width="5%"><BR><BR><B>$max8</B></td>
    </tr>
    <tr>
    <td  align="left" width="2%" align="center" bgcolor="$rat81"></td>
        <td  align="left" width="2%" align="center" bgcolor="$rat82"></td>
        <td  align="left" width="2%" align="center" bgcolor="$rat83"></td>
        <td  align="left" width="2%" align="center" bgcolor="$rat84"></td>
        <td  align="left" width="2%" align="center" bgcolor="$rat85"></td>
        <td  align="left" width="2%" align="center" bgcolor="$rat86"></td>
        <td  align="left" width="2%" align="center" bgcolor="$rat87"></td>
    </tr>
    <tr>
    <td  align="left" width="2%" align="center"></td>
        <td  align="left" width="2%" align="center"></td>
        <td  align="left" width="2%" align="center"></td>
        <td  align="left" width="2%" align="center"></td>
        <td  align="left" width="2%" align="center"></td>
        <td  align="left" width="2%" align="center"></td>
        <td  align="left" width="2%" align="center"></td>
    </tr>
</table>

<table cellspacing="0" cellpadding="1" border="1">
    <tr>
        <td rowspan="3" align="center" width="5%"><BR><BR>9</td>
        <td rowspan="3" align="left" width="61%">$namaaspek9</td>
        <td rowspan="3" align="left" width="5%" align="center"><BR><BR><B>$bobot9</B></td>
        <td bgcolor="#bebebe"  align="left" width="2%" align="center">1</td>
        <td bgcolor="#bebebe"  align="left" width="2%" align="center">2</td>
        <td bgcolor="#bebebe"  align="left" width="2%" align="center">3</td>
        <td bgcolor="#bebebe"  align="left" width="2%" align="center">4</td>
        <td bgcolor="#bebebe"  align="left" width="2%" align="center">5</td>
        <td bgcolor="#bebebe"  align="left" width="2%" align="center">6</td>
        <td bgcolor="#bebebe"  align="left" width="2%" align="center">7</td>
        <td rowspan="3" align="center" width="5%"><BR><BR><B>$min9</B></td>
        <td rowspan="3" align="center" width="5%"><BR><BR><B>$pribadi9</B></td>
        <td rowspan="3" align="center" width="5%"><BR><BR><B>$max9</B></td>
    </tr>
    <tr>
    <td  align="left" width="2%" align="center" bgcolor="$rat91"></td>
        <td  align="left" width="2%" align="center" bgcolor="$rat92"></td>
        <td  align="left" width="2%" align="center" bgcolor="$rat93"></td>
        <td  align="left" width="2%" align="center" bgcolor="$rat94"></td>
        <td  align="left" width="2%" align="center" bgcolor="$rat95"></td>
        <td  align="left" width="2%" align="center" bgcolor="$rat96"></td>
        <td  align="left" width="2%" align="center" bgcolor="$rat97"></td>
    </tr>
    <tr>
    <td  align="left" width="2%" align="center"></td>
        <td  align="left" width="2%" align="center"></td>
        <td  align="left" width="2%" align="center"></td>
        <td  align="left" width="2%" align="center"></td>
        <td  align="left" width="2%" align="center"></td>
        <td  align="left" width="2%" align="center"></td>
        <td  align="left" width="2%" align="center"></td>
    </tr>
</table>

<table cellspacing="0" cellpadding="1" border="1">
    <tr>
        <td rowspan="3" align="center" width="5%"><BR><BR>10</td>
        <td rowspan="3" align="left" width="61%">$namaaspek10</td>
        <td rowspan="3" align="left" width="5%" align="center"><B><BR>$bobot10</B></td>
        <td bgcolor="#bebebe"  align="left" width="2%" align="center">1</td>
        <td bgcolor="#bebebe"  align="left" width="2%" align="center">2</td>
        <td bgcolor="#bebebe"  align="left" width="2%" align="center">3</td>
        <td bgcolor="#bebebe"  align="left" width="2%" align="center">4</td>
        <td bgcolor="#bebebe"  align="left" width="2%" align="center">5</td>
        <td bgcolor="#bebebe"  align="left" width="2%" align="center">6</td>
        <td bgcolor="#bebebe"  align="left" width="2%" align="center">7</td>
        <td rowspan="3" align="center" width="5%"><BR><BR><B>$min10</B></td>
        <td rowspan="3" align="center" width="5%"><BR><BR><B>$pribadi10</B></td>
        <td rowspan="3" align="center" width="5%"><BR><BR><B>$max10</B></td>
    </tr>
    <tr>
    <td  align="left" width="2%" align="center" bgcolor="$rat101"></td>
        <td  align="left" width="2%" align="center" bgcolor="$rat102"></td>
        <td  align="left" width="2%" align="center" bgcolor="$rat103"></td>
        <td  align="left" width="2%" align="center" bgcolor="$rat104"></td>
        <td  align="left" width="2%" align="center" bgcolor="$rat105"></td>
        <td  align="left" width="2%" align="center" bgcolor="$rat106"></td>
        <td  align="left" width="2%" align="center" bgcolor="$rat107"></td>
    </tr>
    <tr>
    <td  align="left" width="2%" align="center"></td>
        <td  align="left" width="2%" align="center"></td>
        <td  align="left" width="2%" align="center"></td>
        <td  align="left" width="2%" align="center"></td>
        <td  align="left" width="2%" align="center"></td>
        <td  align="left" width="2%" align="center"></td>
        <td  align="left" width="2%" align="center"></td>
    </tr>
</table>

<table cellspacing="0" cellpadding="1" border="1">
    <tr>

        <td  align="right" width="71%">$total_bobot</td>
        <td  align="center" width="14%"><h3><B>Nilai Total</B></h3></td>
        <td  align="center" width="5%"><h3><B>$total_min</B></h3></td>
        <td  align="center" width="5%"><h3><B>$total_pribadi</B></h3></td>
        <td  align="center" width="5%"><h3><B>$total_max</B></h3></td>
    </tr>

</table>

</td>

</tr>
</table>

EOD;

$pdf->writeHTML($tbl, true, false, false, false, '');

$pdf->SetFont('helvetica', '', 9);

// -----------------------------------------------------------------------------

$tbl = <<<EOD

<table cellspacing="0" cellpadding="0" border="0">
    <tr>
        <td colspan="3" align="center" height="15px"><B></B></td>
    </tr>
    <tr>
       <td width="71%">

       <table border="1">
            <tr>
                <td  colspan="3" align="left"><B>REKOMENDASI :</B>$rekomendasi</td>

            </tr>
            <tr>
                <td bgcolor="#bebebe" colspan="2" align="center" width="30%"><B>KUALIFIKASI</B></td>
                <td bgcolor="#bebebe"  align="center" width="50%"><B>REKOMENDASI</B></td>
                <td bgcolor="#bebebe"  align="center" width="20%"><B>SKALA</B></td>
            </tr>
            <tr>
                <td width="10%" align="center">K-2</td>
                <td align="left" width="20%">BAIK</td>
                <td width="50%">Dapat Disarankan</td>
                <td width="20%" align="center">400 - 449</td>
            </tr>
           <tr>
                <td width="10%" align="center">K-3</td>
                <td align="left" width="20%">CUKUP</td>
                <td width="50%">Masih Dapat Disarankan</td>
                <td width="20%" align="center">350 - 399</td>
            </tr>
            <tr>
                <td width="10%" align="center">K-4</td>
                <td align="left" width="20%">KURANG</td>
                <td width="50%">Kurang Dapat Disarankan</td>
                <td width="20%" align="center">300 - 349</td>
            </tr>
            <tr>
                <td width="10%" align="center">K-5</td>
                <td align="left" width="20%">BURUK</td>
                <td width="50%">Tidak Dapat Disarankan</td>
                <td width="20%" align="center">299 - Ke bawah</td>
            </tr>
       </table>

       <br><br>



       </td>

       <td width="2%"></td>

       <td width="27%">

            <table>
            <tr>
            <td align="center">$tempattes, $tgltes</td>
            </tr>
            <tr>
            <td align="center">A.n. Psikolog Pemeriksa</td>
            </tr>
            <tr>
            <td align="center"><BR><BR>TTD<BR></td>
            </tr>
            <tr>
            <td align="center">$ttd</td>
            </tr>
            <tr>
            <td align="center">$himpsi</td>
            </tr>
            </table>
       </td>

    </tr>

</table>
EOD;

$pdf->writeHTML($tbl, true, false, false, false, '');
//Close and output PDF document
$pdf->Output('psikotes.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+
