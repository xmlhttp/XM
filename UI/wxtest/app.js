//app.js
const EARTH_RADIUS = 6378137.0;    //单位M
const PI = Math.PI;
App({
  onLaunch: function () {
    //弹框加载
    wx.showLoading({
      title: '加载中',
      mask: true
    })
    // 展示本地存储能力
    var $this = this
    // 登录
    wx.login({
      success: res => {
        // 发送 res.code 到后台换取 openId, sessionKey, unionId
        if (res.code) {
          console.log(res.code)
          //发起网络请求
          wx.request({
            url: $this.globalData.URL + "/index.php?s=/Home/Index/onLogin",
            data: { code: res.code },
            method: "POST",
            header: $this.globalData.header,
            success: function (res) {
              console.log(res.data)
              if (res.data.status.err == 0) {

                var userkey={
                  uid: res.data.uid,
                  sessionid: res.data.sessionid
                }
                var user = {
                  cele: res.data.cele,
                  cmoney: res.data.cmoney,
                  headimg: res.data.headimg,
                  nickname: res.data.nickname
                }
                wx.setStorageSync('userkey', userkey)
                wx.setStorageSync('user', user)

              } else {
                wx.showModal({
                  title: '消息提示',
                  content: res.data.status.msg,
                  showCancel: false,
                  confirmText: '返回',
                  success: function (res) {
                    wx.navigateBack()
                  }
                })
              }
            }
          })
        } else {
          console.log('获取用户登录态失败！' + res.errMsg)
          wx.showModal({
            title: '消息提示',
            content: res.errMsg,
            showCancel: false,
            confirmText: '返回',
            success: function (res) {
              wx.navigateBack()
            }
          })

        }
      }
    })
  },
  globalData: {
    //URL: "https://budian.richcomm.com.cn",
    URL: "http://139.199.221.53:9002",
    header: {"Content-Type":"application/x-www-form-urlencoded"},
    siteArr: null,
    loacal: {
      longitude: 113.43611208062396,
      latitude: 23.168447548770743
    }
  },
  /**
   * 全局弹框提示
   */
  errAlert:function(str){
    wx.showModal({
      title: '消息提示',
      content: str,
      showCancel: false,
      confirmText: '确定'
    })
  },
  /**********
   * 获取参数
  */
  GetUrlParam: function (strUrl, Param) {
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
  /**********
  * 弧度与角度转换
 */
  getRad: function (d) {
    return d * PI / 180.0;
  },
  /**
  * 计算直线距离
  */
  getFlatternDistance: function (lat1, lng1, lat2, lng2) {

    var f = this.getRad((lat1 + lat2) / 2);
    var g = this.getRad((lat1 - lat2) / 2);
    var l = this.getRad((lng1 - lng2) / 2);

    var sg = Math.sin(g);
    var sl = Math.sin(l);
    var sf = Math.sin(f);

    var s, c, w, r, d, h1, h2;
    var a = EARTH_RADIUS;
    var fl = 1 / 298.257;

    sg = sg * sg;
    sl = sl * sl;
    sf = sf * sf;

    s = sg * (1 - sl) + (1 - sf) * sl;
    c = (1 - sg) * (1 - sl) + sf * sl;

    w = Math.atan(Math.sqrt(s / c));
    r = Math.sqrt(s * c) / w;
    d = 2 * w * a;
    h1 = (3 * r - 1) / 2 / c;
    h2 = (3 * r + 1) / 2 / s;
    return (d * (1 + fl * (h1 * sf * (1 - sg) - h2 * (1 - sf) * sg)) / 1000).toFixed(2);
  }
})