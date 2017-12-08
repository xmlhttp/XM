// pages/userlist/userlist.js
const app = getApp()
var userkey = null
Page({

  /**
   * 页面的初始数据
   */
  data: {
    user: {
      cele: "0.0",
      cmoney: "0.00",
      headimg: '/resources/headimg.jpg',
      nickname: '未授权'
    },
    maxid: 0,
    uid:0,
    orderlist: [],
    Loading: false, //"上拉加载"的变量，默认false，隐藏  
    LoadingComplete: false  //“没有数据”的变量，默认false，隐藏  
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    var $this = this;
    var user = wx.getStorageSync("user")
    userkey = wx.getStorageSync("userkey")
    this.setData({ user: user,uid:userkey.uid })
    //用户信息授权
    wx.getSetting({
      success: res => {
        if (!res.authSetting['scope.userInfo']) {
          wx.authorize({
            scope: 'scope.userInfo',
            success() {
              //wx.getUserInfo()
              $this.setUser()
            }
          })
        } else {
          $this.setUser()
        }
      }
    })
    this.getOrder();
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
   * 设置用户
   */
  setUser: function () {
    //请求用户和头像
    var $this = this;
    wx.getUserInfo({
      success: function (res) {
        var userInfo = res.userInfo
        if (userInfo.nickName != $this.data.user.nickname || userInfo.avatarUrl != $this.data.user.headimg) {
          var user = wx.getStorageSync("user")
          user.nickname = userInfo.nickName
          user.headimg = userInfo.avatarUrl
          $this.setData({user: user})
          $this.chkNick(userInfo)
        }
      }
    })
  },
  /**
   * 获取列表
   */
  getOrder: function () {
    var maxid = this.data.maxid
    var $this = this;
    $this.setData({ Loading: true })
    wx.request({
      url: app.globalData.URL + "/index.php?s=/Home/Index/getOrder",
      data: {
        uid: userkey.uid,
        sessionid: userkey.sessionid,
        maxid: maxid
      },
      method: "POST",
      header: app.globalData.header,
      success: function (res) {
        console.log(res.data)
        if (res.data.status.err == 0) {
          var orderlist = $this.data.orderlist;
          var maxid = res.data.maxid
          orderlist.push.apply(orderlist, res.data.data);
          var utel = res.data.tel
          if (res.data.data.length >= 10) {
            $this.setData({ maxid: maxid, Loading: false, orderlist: orderlist })
          } else {
            $this.setData({ maxid: maxid, Loading: false, LoadingComplete: true, orderlist: orderlist })
          }

        } else {
          app.errAlert(res.data.status.msg)
        }
      },
      fail: function () {
        app.errAlert("请求执行错误")
      }
    });
  },
  /**
   * 上拉事件
   */
  loadScrollLower: function () {
    if (!this.data.Loading && !this.data.LoadingComplete) {
      this.setData({ Loading: true })
      this.getOrder();
    }
  },
  /**
   * 修改用户信息
   */
  chkNick: function (userInfo) {
    var $this = this;
    wx.request({
      url: app.globalData.URL + "/index.php?s=/Home/Index/chageNick",
      data: {
        uid: userkey.uid,
        sessionid: userkey.sessionid,
        nickname: userInfo.nickName,
        headimg: userInfo.avatarUrl
      },
      method: "POST",
      header: app.globalData.header,
      success: function (res) {
        if(res.data.status.err==0){
          var user = {
            cele: $this.data.cele,
            cmoney: $this.data.cmoney,
            headimg: userInfo.avatarUrl,
            nickname: userInfo.nickName
          }
          wx.setStorageSync('user', user)
        }else{
          app.errAlert(res.data.status.msg)
        }
       }
    });
  }

})