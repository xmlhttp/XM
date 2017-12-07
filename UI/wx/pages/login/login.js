var md5 = require("../MD5.js")
var checkNetWork = require("../CheckNetWork.js")
const app = getApp()
Page({

  /**
   * 页面的初始数据
   */
  data: {
    utel: '',
    ucode:'',
    getCodeBtnProperty: {
      titileColor: '#4b4b4b',
      disabled: true,
      loading: false,
      title: '获取验证码'
    },
    utel: null
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {

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

  },
  getPhoneNumber: function (e) {
    var encode = e.detail.encryptedData;
    var ivcode = e.detail.iv;
    var $this = this;
    console.log(wx.getStorageSync("sessionKey"))
    wx.request({
      url: app.globalData.URL + "/index.php?s=/Home/Index/getTel",
      data: {
        encode: encode,
        ivcode: ivcode,
        sessionKey: wx.getStorageSync("sessionKey")
      },
      method: "POST",
      header: {
        "Content-Type": "application/x-www-form-urlencoded"
      },
      success: function (res) {
        if (res.data.status.err == 0) {
          var utel = res.data.tel
          $this.setData({ utel: utel })
        }
      },

      telInput: function (e) {
        this.setData({
          utel: e.detail.value
        })
      },
      codeInput: function (e) {
        this.setData({
          utel: e.detail.value
        })
      },


    })




  }
})