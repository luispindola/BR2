<!--Ing. Luis Spíndola-->
<!--luispindola78@gmail.com-->
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
    <meta name="BancoDeReactivos" content="MSHTML 8.00.6001.18928"   >
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <style>
        a.navwhite:link { text-decoration: none; color: #ffffff; font-family: Verdana, Arial, sans-serif; font-size: 10px; font-weight: bold; }
        a.navwhite:visited { text-decoration: none; color: #ffffff; font-family: Verdana, Arial, sans-serif; font-size: 10px; font-weight: bold; }
        a.navwhite:hover { text-decoration: underline; color: #ffffff; font-family: Verdana, Arial, sans-serif; font-size: 10px; font-weight: bold; }
        a.navblack:link { text-decoration: none; color: #000000; font-family: Verdana, Arial, sans-serif; font-size: 10px; font-weight: bold; }
        a.navblack:visited { text-decoration: none; color: #000000; font-family: Verdana, Arial, sans-serif; font-size: 10px; font-weight: bold; }
        a.navblack:hover { text-decoration: underline; color: #000000; font-family: Verdana, Arial, sans-serif; font-size: 10px; font-weight: bold; }
        h1 { font-family: Arial, sans-serif; font-size: 30px; color: #517901;}
        h2 { font-family: Arial, sans-serif; font-size: 18px; color: #517901;}
        body,p,b,i,em,dt,dd,dl,sl,caption,th,td,tr,u,blink,select,option,form,div,li { font-family: Arial, sans-serif; font-size: 12px; }
    </style>
    <!--Inicia Codigo MENU-->
    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
    <link rel="stylesheet" type="text/css" href="http://spin100.com/SPIN/bancodereactivos/bancodereactivos/ddsmoothmenu.css" />
    <link rel="stylesheet" type="text/css" href="http://spin100.com/SPIN/bancodereactivos/bancodereactivos/ddsmoothmenu-v.css" />
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script>
    <script type="text/javascript" src="http://spin100.com/SPIN/bancodereactivos/bancodereactivos/ddsmoothmenu.js"></script>
    <script type="text/javascript">
        ddsmoothmenu.init({
            mainmenuid: "smoothmenu1", //menu DIV id
            orientation: 'h', //Horizontal or vertical menu: Set to "h" or "v"
            classname: 'ddsmoothmenu', //class added to menu's outer DIV
            customtheme: ["#333333", "#517901"],
            contentsource: "markup" //"markup" or ["container_id", "path_to_menu_file"]
        })
    </script>
    <!--Fin Codigo Menu-->
</head>
<body>
    <table border="0" cellspacing="0" cellpadding="0" width=100% align="center">
        <tbody>
            <tr>
                <td>
                    <!--Inicia Menu-->
                    <div id="smoothmenu1" class="ddsmoothmenu">
                        <?php echo($menu); ?>
                        <br style="clear: left" />
                    </div>
                    <!--Fin MENU-->
                </td>
            </tr>
            <tr>
                <td>
                    <p>
                    <?php echo($datos_inicio); ?>
                    </p>
                </td>
            </tr>
        </tbody>
    </table>
</body>
</html>