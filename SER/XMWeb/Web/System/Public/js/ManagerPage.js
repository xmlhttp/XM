    var M = {
        init: function() {
            var _this = this;
            _this.adjust()
            _this.event()
			window.BMap_loadScriptTime = (new Date).getTime();
			setTimeout(function(){
				//高德地图
				$.getScript("https://webapi.amap.com/maps?v=1.4.0&key=edf08007878f22303f8d52cf5c59b707")
				//百度地图
				$.getScript("https://api.map.baidu.com/getscript?v=2.0&ak=0hyIpklrNXG3cKHIVgstWGcF&services=&t="+window.BMap_loadScriptTime)	

				$.getScript("https://open.map.qq.com/apifiles/2/4/79/main.js")		
			},100)
        },
        event: function() {
            var _this = this;
            //内页左侧导航点击
            $(".left_nav>a").click(function() { $(this).toggleClass("cura").next('.left_sub').slideToggle(250).end().siblings("a").removeClass("cura").stop(true).next('.left_sub:visible').slideToggle(250) });
            //窗口改变后动态计算
            $(window).resize(function() { _this.adjust(); });
            //点击导航
            var items = $(".left_sub>.left_sub1>a")
            items.click(function() {
                items.removeClass("a_hover")
                $(this).addClass("a_hover")
            })
            //设置版本
            var ver = _this.GetUrlParam("ver")
            if (ver == "") { 
            ver=0
        }
        $("#vers>a:eq(" + ver + ")").addClass("a_active").siblings("a").removeClass("a_active")
       
        },
        adjust: function() {//设置窗体大小
            var conHei = $(window).outerHeight(true) - 50
            $(".info").height(conHei);
            $(".info_right").height(conHei);
            $(".info_left,.info_txt").height(conHei);
        },
        GetUrlParam: function(Param) { //获取get参数
            var strUrl = document.location.search.toString();
            var lisUrl = strUrl.split('?');
            if (lisUrl.length > 1) {
                var lisParam = lisUrl[1].split('&');
                for (var i = 0; i < lisParam.length; i++) {
                    var strParm = lisParam[i].split('=');
                    if (strParm[0] == Param) {
                        return strParm[1];
                    }
                }
                return "";
            } else {
                return ""
            }
        },
		showmap:function(a){
			$("#maskbg").fadeIn(200);
			$("<div>",{"class":"mapview","id":"mapview"}).html('<div class="mapinfo" id="mapinfo"></div><a class="mapclose" id="mapclose">×</a><input type="button" class="btn1" id="getcenterpoint" value="确定坐标" /><input type="button" class="btn1" id="gopoint" value="返回定位点" style="right:162px" /> ').appendTo("body").fadeIn(200);
			var icosrc='/Web/System/Public/images/marker.png'
			var map,marker,icon;
			if(a["type"]==0){
				map = new AMap.Map('mapinfo',{
    				zoom: a["z"],
    				center: [a["y"],a["x"]]
				});
				icon = new AMap.Icon({
        			image : icosrc,
        			size : new AMap.Size(22,30)
				});
				marker = new AMap.Marker({
					position: [a["y"],a["x"]],
					offset: new AMap.Pixel(-11,-30),
					icon:icon,
					draggable:true
    			});
				marker.setMap(map);
				$("#gopoint").click(function(){
					map.setCenter(marker.getPosition());	
				})
				$("#getcenterpoint").click(function(){
					var c={"J":marker.getPosition().getLat(),"D":marker.getPosition().getLng()};
					frame_right.setpoint(c,0)
					$("#maskbg").hide();
					$("#mapview").remove()	
				})
			}else if(a["type"]==1){
				map = new BMap.Map("mapinfo");          // 创建地图实例  
				var point = new BMap.Point(a["y"], a["x"]);  // 创建点坐标  
				map.centerAndZoom(point, a["z"]);  
				map.enableScrollWheelZoom();  
				icon = new BMap.Icon(icosrc, new BMap.Size(22, 30), {    
   					anchor: new BMap.Size(12, 30)       
 				}); 
				marker = new BMap.Marker(point, {icon: icon,enableDragging:true});    
 				map.addOverlay(marker); 
				 
				$("#gopoint").click(function(){
					map.setCenter(marker.getPosition());	
				})
				$("#getcenterpoint").click(function(){
					var c={"J":marker.getPosition().lat,"D":marker.getPosition().lng};
					frame_right.setpoint(c,1)
					$("#maskbg").hide();
					$("#mapview").remove()	
				})					
			}else if(a["type"]==2){
				var myLatlng = new qq.maps.LatLng(a["x"], a["y"]);
 				var myOptions = {
    				zoom: a["z"],
   					center: myLatlng,
   					mapTypeId: qq.maps.MapTypeId.ROADMAP
  				}
  				map = new qq.maps.Map(document.getElementById("mapinfo"), myOptions);
				icon = new qq.maps.MarkerImage(icosrc,new qq.maps.Size(22, 30),new qq.maps.Point(0, 0),new qq.maps.Point(12, 30));
				marker = new qq.maps.Marker({
					position: myLatlng,
					map: map,
					icon:icon,
					draggable:true
				});
				$("#gopoint").click(function(){
					map.setCenter(marker.getPosition());	
				})
				$("#getcenterpoint").click(function(){
					var c={"J":marker.getPosition().getLat(),"D":marker.getPosition().getLng()};
					frame_right.setpoint(c,2)
					$("#maskbg").hide();
					$("#mapview").remove()	
				})	
				
				
			}
			 
			 
			$("#mapclose").click(function(){
				$("#maskbg").hide();
				$("#mapview").remove()
			})
		},
		build:function(a){
			$("#maskbg").fadeIn(200);
			
			$("<div>",{"class":"popewm","id":"popewm"}).html('<div class="popewm1" id="popewm1"><img src="/System.php?s=/System/PileListAll/Qrcode&id='+a+'" /></div><a class="popclose" id="popclose">×</a>').appendTo("body").fadeIn(200);
			$("#popclose").click(function(){
				$("#maskbg").hide();
				$("#popewm").remove()
			})		
		},
		setpark:function(x,y,r,d){
			$("#maskbg").fadeIn(200);
			$("<div>",{"class":"landview","id":"landview"}).html('<div class="tab_tit topbar"><a class="popclost" id="popclost">×</a><a class="popsubmit" id="landqr">确定</a><table style=" float:left; width:auto"><tr><td style="width:80px; text-align:right">旋转角度：</td><td style="width:120px; text-align:left"><input type="text" class="input1" id="rote" style="margin-right:5px; width:100px" value="'+r+'" /></td><td><input type="button" class="btn" id="btnset" value="设置" /></td></tr></table></div><iframe id="frame_land" frameborder="0" name="frame_land" 	scrolling="auto" style="height:470px;width:700px"  src="/System.php?s=/System/PileListAll/Park&sid='+d+'&x='+x+'&y='+y+'&r='+r+'"></iframe>').appendTo("body").fadeIn(200);
			$("#popclost").click(function(){
				$("#maskbg").fadeOut(200);
				$("#landview").remove()
			})
			$("#btnset").click(function(){
				if(isNaN($("#rote").val())){
					alert("旋转角度必须为数字！");	
				}else{
					frame_land.window.rot($("#rote").val())
					
				}	
			})
			$("#landqr").click(function(){
				frame_land.window.getdata()
				$("#maskbg").fadeOut(200);
				$("#landview").remove()
			})
		},
		Landdata:function(x,y,r){
			frame_right.window.setland(x,y,r);
		}
				
    }
    $(function() {
		window.HOST_TYPE = "2"; 
		window.BMap_loadScriptTime = (new Date).getTime(); 
		window.qq = window.qq || {};
		qq.maps = qq.maps || {};
		window.soso || (window.soso = qq);
		soso.maps || (soso.maps = qq.maps);
		(function () {
    		qq.maps.__load = function (apiLoad) {
        		delete qq.maps.__load;
       			 apiLoad([["2.4.79","",0],["open.map.qq.com/","apifiles/2/4/79/mods/","open.map.qq.com/apifiles/2/4/79/theme/",true],[1,18,34.519469,104.461761,4],[1508062015645,"pr.map.qq.com/pingd","pr.map.qq.com/pingd"],["apis.map.qq.com/jsapi","apikey.map.qq.com/mkey/index.php/mkey/check","sv.map.qq.com/xf","sv.map.qq.com/boundinfo","sv.map.qq.com/rarp","apis.map.qq.com/api/proxy/search","apis.map.qq.com/api/proxy/routes/","confinfo.map.qq.com/confinfo"],[[null,["rt0.map.gtimg.com/tile","rt1.map.gtimg.com/tile","rt2.map.gtimg.com/tile","rt3.map.gtimg.com/tile"],"png",[256,256],3,19,"114",true,false],[null,["m0.map.gtimg.com/hwap","m1.map.gtimg.com/hwap","m2.map.gtimg.com/hwap","m3.map.gtimg.com/hwap"],"png",[128,128],3,18,"110",false,false],[null,["p0.map.gtimg.com/sateTiles","p1.map.gtimg.com/sateTiles","p2.map.gtimg.com/sateTiles","p3.map.gtimg.com/sateTiles"],"jpg",[256,256],1,19,"101",false,false],[null,["rt0.map.gtimg.com/tile","rt1.map.gtimg.com/tile","rt2.map.gtimg.com/tile","rt3.map.gtimg.com/tile"],"png",[256,256],1,19,"",false,false],[null,["sv0.map.qq.com/hlrender/","sv1.map.qq.com/hlrender/","sv2.map.qq.com/hlrender/","sv3.map.qq.com/hlrender/"],"png",[256,256],1,19,"",false,false],[null,["rtt2.map.qq.com/rtt/","rtt2a.map.qq.com/rtt/","rtt2b.map.qq.com/rtt/","rtt2c.map.qq.com/rtt/"],"png",[256,256],1,19,"",false,false],null,[["rt0.map.gtimg.com/vector/","rt1.map.gtimg.com/vector/","rt2.map.gtimg.com/vector/","rt3.map.gtimg.com/vector/"],[256,256],3,18,"114",["rt0.map.gtimg.com/icons/","rt1.map.gtimg.com/icons/","rt2.map.gtimg.com/icons/","rt3.map.gtimg.com/icons/"]],null],["s.map.qq.com/TPano/v1.1.2/TPano.js","map.qq.com/",""]],loadScriptTime);
    };
			var loadScriptTime = (new Date).getTime();
		})();
        M.init();
		
    })