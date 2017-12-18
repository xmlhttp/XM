// pages/map/map.js
const app = getApp()
var userkey = null;
Page({

  /**
   * 页面的初始数据
   */
  data: {
    ismap: false,
    mapimg: ''
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    var sid = options.sid;
    var $this = this
    if (userkey == null) {
      userkey = wx.getStorageSync("userkey")
    }
    console.log(sid)
    wx.request({
      url: app.globalData.URL + "/index.php?s=/Home/Index/Site_None",
      data: {
        sid: sid,
        uid: userkey.uid,
        sessionid:userkey.sessionid
      },
      method: "POST",
      header: app.globalData.header,
      success: function (res) {
        console.log(res.data)
        if (res.data.status.err == 0) {
          $this.setData({ mapimg: res.data.img, ismap:true })

            wx.setNavigationBarTitle({
              title: res.data.sitename + "-车位图"
            })
        } else if (res.data.status.err == 1) {
          wx.showModal({
            title: '消息提示',
            content: res.data.status.msg,
            showCancel: false,
            confirmText: '返回',
            success: function () {
              wx.navigateBack()
            }
          })
        } else if (res.data.status.err == 2){
            wx.setNavigationBarTitle({
              title: res.data.sitename + "-车位图"
            })
            $this.setData({ ismap: false})
        }
      }
    })
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

  }
  //事件处理函数
})