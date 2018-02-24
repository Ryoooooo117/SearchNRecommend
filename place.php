<!DOCTYPE html>
<html>
    <head>
        <meta>
        <style>
            #searchPanel {
                position: absolute;
                top: 10px;
                left: 25%;
                background-color: #fff;
                padding: 5px;
                border: 1px solid #999;
                text-align: left;
                font-family: 'Roboto','sans-serif';
                line-height: 30px;
                padding-left: 10px;
              }
            
            #requestPanel {
                position: absolute;
                top: 270px;
                left: 10%;
                width: 80%;
                background-color: #fff;
                padding: 5px;
                border: 1px solid #999;
                text-align: center;
                font-family: 'Roboto','sans-serif';
                line-height: 30px;
                padding-left: 10px;
            }
            
        </style>
        <script language="JavaScript">
            
            var myLat, myLon;
            function getCurLoc(){
                var xmlhttp=new XMLHttpRequest(); 
                xmlhttp.open("GET","http://ip-api.com/json",false);
                xmlhttp.send();
                jsonDoc = xmlhttp.responseText;
                if( jsonDoc!=null ){
                    document.getElementsByName("search")[0].disabled = false;
                    var obj = JSON.parse(jsonDoc);
                    myLat = obj.lat;
                    myLon = obj.lon;
                }
            }
            
            function radioEnable(){
                var radio = document.getElementsByName("startLoc");
                if( radio[0].checked ){
                    document.getElementsByName("otherLocation")[0].disabled = true;
                }
                else{
                    document.getElementsByName("otherLocation")[0].disabled = false;
                    console.log(document.getElementsByName("otherLocation")[0].value);
                }
            }
            
            function ValidateForm(){
                form = document.forms["form"];
                form.distance.value = form.distance.value.length < 1? form.distance.placeholder:form.distance.value;
                
                if( form.otherLocation.value.length < 1 ){
                    form.curLocation.value = myLat+","+myLon;
                }
                console.log(form.curLocation.value);
                
                var xmlhttp=new XMLHttpRequest(); 
                xmlhttp.open("POST","place.php",true);
                xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                var str = "ajax";
                xmlhttp.send('var_name='+str);
                return false;
            }
            
        </script>
    </head>
    <body onload="getCurLoc()">
        <form id="form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post" onsubmit="return ValidateForm();" >
            <div id="searchPanel">
                <span>Travel and Entertainment Search</span>
                <hr>
                <table>
                    <tr>
                        <td>
                            Keyword <input name="keyword" type="textbox" value="<?php echo $keyword;?>" required>
                        </td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>
                            Category <select name="category">
                                <option selected>default</option>
                                <option >cafe</option>
                                <option >bakery</option>
                                <option >restaurant</option>
                                <option >beauty</option>
                                <option >salon</option>
                                <option >casino</option>
                                <option >movie theater</option>
                                <option >lodging</option>
                                <option >airport</option>
                                <option >train station</option>
                                <option >subway station</option>
                                <option >bus station</option>
                            </select> 
                        </td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>
                            Distance (miles) <!--input name="distance" value="10" onFocus="if(value==defaultValue){value='';this.style.color='#000'}" onBlur="if(!value){value=defaultValue; this.style.color='#999'}" style="color:#999"-->
                            <input name="distance" placeholder="10" value="<?php echo $distance;?>">
                        </td>
                        <td>
                            from<input name="startLoc" type="radio" onclick="radioEnable()" checked>
                            Here
                            <input name="curLocation" type="hidden" >
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td> <input type="radio" name="startLoc" onclick="radioEnable()" ><!--input name="otherLocation" value="location" onFocus="if(value==defaultValue){value='';this.style.color='#000'}" onBlur="if(!value){value=defaultValue; this.style.color='#999'}" style="color:#999" disabled="disabled"--><input name="otherLocation" placeholder="location" disabled="disabled" required>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input name="search" type="submit" value="Search" disabled=true>
                            <input name="clear" type="button" value="Clear">
                        </td>
                    </tr>
                </table>
            </div>
        </form>
    </body>
</html>

<?php

    function loadJson($url){
        $headers = get_headers($url);
        $state = substr($headers[0], 9, 3);
        if($state != "404"){
            $content = file_get_contents($url);
            $Json =  json_decode($content);
            return $Json;
        }
        return null;
    }

    echo "<script>console.log( 'ok1');</script>";

    $apiKey = $keyword = $distance = $loc = $myloc = $category ="";
    if ($_SERVER["REQUEST_METHOD"] == "POST"){
        echo "<script>console.log( 'ok2');</script>";
        $apiKey = "AIzaSyA_PVMVVOphJ7orAqwqN2TTrnf_TzLJTd4";
        /*
        $keyword = $_POST["keyword"];
        $category = $_POST["category"];
        $distance = $_POST["distance"];
        $myloc = $_POST["curLocaion"];
        if( $_POST["curLocation"] == '' ){
            $otherLocation = str_replace(' ', '+', $_POST["otherLocation"]);
            $otherLocation = strtolower($otherLocation);
            $otherLocUrl = "https://maps.googleapis.com/maps/api/geocode/json?address=".$otherLocation."&key=".$apiKey;
            //echo "<script>console.log( 'location url: " . $otherLocUrl . "' );</script>";
            $otherLocJson = loadJson($otherLocUrl);
            if( $otherLocJson !=null ){
                $loc = $otherLocJson->results[0]->geometry->location->lat.",".$otherLocJson->results[0]->geometry->location->lng;
            }
        }
        else $loc = $_POST["curLocation"];
        
        echo "keyword: ".$keyword."<br>";
        echo "distance km: ".$distance*1609.34."<br>";
        echo "myLoc: ".$loc."<br>";
        echo "category: ".$category."<br>";
        
        $requestUrl = "https://maps.googleapis.com/maps/api/place/nearbysearch/json?location=".$loc."&radius=".$distance*1609.34."&type=".$category."&keyword=".$keyword."&key=".$apiKey;
        echo "<script>console.log( 'request url: " . $requestUrl . "' );</script>";
        
        $requestJson = loadJson($requestUrl);
        echo "<script>console.log( 'json: " . $requestJson->results[1]->name. "' );</script>";
        
        $resultCount = count($requestJson->results);
        echo "<script>console.log( 'number: " . $resultCount. "' );</script>";
        
         */
        echo "<div id=\"requestPanel\"></div>";
        echo "<script>document.getElementById(\"requestPanel\").innerHTML = \"<table><tr><th>Category</th><th>Name</th><th>Address</th></tr></table>\"</script>";
        //$requestJson->results[i]->icon
        //$requestJson->results[i]->name
        //$requestJson->results[i]->vicinity
    }
            
?>