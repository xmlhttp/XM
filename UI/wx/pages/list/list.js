// pages/list/list.js
const app = getApp()
Page({

  /**
   * 页面的初始数据
   */
  data: {
    url: null,
    siteArr: null
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {


   // this.setData({ url: app.globalData.URL, siteArr: app.globalData.siteArr })

    var $this = this;
    var locax = app.globalData.loacal.latitude;
    var locay = app.globalData.loacal.longitude;
    var temparr = JSON.parse(JSON.stringify(app.globalData.siteArr))
   // temparr = app.globalData.siteArr
    temparr.forEach(function (value, index, array) {
      array[index]["distan"] = parseFloat(app.getFlatternDistance(locax, locay, value.latitude, value.longitude));
    })
    temparr.sort($this.jsSort("distan"))
    this.setData({ url: app.globalData.URL, siteArr: temparr})
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
  /**
 * 排序规则
 */
  jsSort: function (name) {
    return function (o, p) {
      var a, b;
      if (typeof o === "object" && typeof p === "object" && o && p) {
        a = o[name];
        b = p[name];
        if (a === b) {
          return 0;
        }
        if (typeof a === typeof b) {
          return a < b ? -1 : 1;
        }
        return typeof a < typeof b ? -1 : 1;
      }
      else {
        throw ("error");
      }
    }
  }


})