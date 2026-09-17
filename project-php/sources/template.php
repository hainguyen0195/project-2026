<?php
    $d->reset();
    $sql = "select thumb_vi from #_photo where type='favicon'";
    $d->query($sql);
    $favicon = $d->fetch_array();

    $d->reset();
    $sql = "select noidung_$lang as noidung from #_company where type='footer'  limit 0,1";
    $d->query($sql);
    $footer = $d->fetch_array();

    $d->reset();
    $sql= "select photo_$lang_active as photo from #_photo where type='logo'";
    $d->query($sql);
    $logo = $d->fetch_array();

    $d->reset();
    $sql= "select photo,link,hienthi from #_info where type='bocongthuong'";
    $d->query($sql);
    $bocongthuong = $d->fetch_array();

    $d->reset();
    $sql= "select photo_$lang as photo from #_photo where type='banner'";
    $d->query($sql);
    $banner = $d->fetch_array();

    $d->reset();
    $sql= "select photo_$lang_active as photo from #_photo where type='share'";
    $d->query($sql);
    $share = $d->fetch_array();

    $d->reset();
    $sql= "select photo_$lang_active as photo from #_photo where type='br_banner'";
    $d->query($sql);
    $br_banner = $d->fetch_array();

    $d->reset();
    $sql = "select ten_$lang as ten,id,url,photo from #_lkweb where type='mxh' and hienthi=1 order by stt, id desc";
    $d->query($sql);
    $mxh = $d->result_array();

    $d->reset();
    $sql = "select ten_$lang as ten,id,tenkhongdau,photo from #_product_list where type='product' and hienthi=1 order by stt,id desc";
    $d->query($sql);
    $list = $d->result_array();

    $d->reset();
    $sql = "select ten_$lang,id,link,photo_$lang,thumb_$lang from #_photo where hienthi=1 and type='slider' order by stt, id desc";
    $d->query($sql);
    $slider = $d->result_array();

    $d->reset();
    $sql = "select mota,photo_$lang_active,hienthi,link from #_photo where type='bando' ";
    $d->query($sql);
    $bando = $d->fetch_array();

    $d->reset();
    $d->query("select id,ten_$lang,thumb,photo,mota_$lang,tenkhongdau,ngaytao,alt_$lang from #_baiviet where hienthi=1 and type='chinhsach' and noibat=1 order by stt asc,id desc");
    $chinhsach=$d->result_array();

    $d->reset();
    $sql= "select photo_$lang_active as photo,vitri from #_photo where type='dongdau'";
    $d->query($sql);
    $dongdau = $d->fetch_array();
    
    $_SESSION['dongdau']= $dongdau['photo'];
    $_SESSION['vitri']= $dongdau['vitri'];


?>