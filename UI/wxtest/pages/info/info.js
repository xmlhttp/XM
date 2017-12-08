// pages/info/info.js
const app = getApp()
Page({

  /**
   * 页面的初始数据
   */
  data: {
    imgUrls: null,
    indicatorDots: true,
    autoplay: true,
    interval: 5000,
    duration: 1000,
    csite: null,
    distan:0,
    devlist:[]
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    wx.showLoading({
      title: '加载中',
      mask: true
    })
    var sid = options.sid;
    var $this=this;
    console.log(sid)
    wx.showShareMenu({
      withShareTicket: true
    })
  
    wx.request({
      url: app.globalData.URL + "/index.php?s=/Home/Index/Site_one",
      data: {
        sid: sid,
        uid: wx.getStorageSync("uid"),
        sessionid: wx.getStorageSync("sessionid")
      },
      method: "POST",
      header: {
        "Content-Type": "application/x-www-form-urlencoded"
      },
      success: function (res) {
        wx.hideLoading()
        console.log(res.data)
        if (res.data.status.err == 0) {
          var imgarr = res.data.siteinfo.siteimgs.split("|");
          var imgarr1 = [];
          for (var i = 0; i < imgarr.length; i++) {
            if (imgarr[i] != "" && imgarr[i] != null) {
              imgarr1.push(app.globalData.URL + imgarr[i])
            }
          }
          var distan = app.getFlatternDistance(app.globalData.loacal.latitude, app.globalData.loacal.longitude, res.data.siteinfo.latitude, res.data.siteinfo.longitude);

          wx.setNavigationBarTitle({
            title: res.data.siteinfo.sitename
          })
          $this.setData({
            devlist: res.data.data,
            csite: res.data.siteinfo, 
            imgUrls: imgarr1, 
            distan: distan
          });
         
        }else{
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
      },
      fail:function(res){
        wx.hideLoading()
        wx.showModal({
          title: '消息提示',
          content: '网络错误',
          showCancel: false,
          confirmText: '返回',
          success: function (res) {
            wx.navigateBack()
          }
        })
      }
    });




  },

  /**
   * 生命周期函数--监听页面初次渲染完成
   */
  onReady: function () {

  },

  /**
   * 生命周期函数--监听页面显示
   */
  onShow: function () {
    wx.showShareMenu({
      withShareTicket: true
    })
  },

  /**
   * 生命周期函数--监听页面隐藏
   */
  onHide: function () {

  },

  /**
   * 生命周期函数--监听页面卸载
   */
  onUnload: function () {

  },

  /**
   * 页面相关事件处理函数--监听用户下拉动作
   */
  onPullDownRefresh: function () {

  },

  /**
   * 页面上拉触底事件的处理函数
   */
  onReachBottom: function () {

  },

  /**
   * 用户点击右上角分享
   */
  onShareAppMessage: function () {
    var sitename = this.data.csite.sitename
    return {
      title: '微信扫码充电',
      desc: '跟你推荐一款提供方便、快捷为汽车充电的小程序',
      path: "pages/index/index"
    }
  },
   /**
   * 用户点击电话
   */
  connser:function(){
    console.log(this.data.csite)
    var tel = this.data.csite.sitetel
    if (tel == "") {
      wx.showModal({
        title: '提示',
        content: '商家未设置电话',
        showCancel: false
      })
    } else {
      wx.makePhoneCall({
        phoneNumber: tel,
        fail: function () {
          wx.showModal({
            title: '消息提示',
            content: '客服电话：' + tel,
            confirmText: '复制号码',
            success: function (res) {
              if (res.confirm) {
                wx.setClipboardData({
                  data: tel,
                })
              }
            }
          })
        }
      })
    }
  },
   /**
   * 用户点击导航
   */
  clknav:function(){
    var latitude = this.data.csite.latitude
    var longitude = this.data.csite.longitude
    var sitename = this.data.csite.sitename
    var siteadd = this.data.csite.siteadd
    wx.openLocation({
      latitude: latitude,
      longitude: longitude,
      name: sitename,
      address: siteadd
    })
  },
  /**
   * 用户点击车位
   */
  gocar:function(){
    var sid = this.data.csite.sid
    var url = this.data.csite.sitemap
    var sitename = this.data.csite.sitename
    wx.navigateTo({ url: '/pages/map/map?sid=' + sid})
  }
})