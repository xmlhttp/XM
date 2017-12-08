// pages/select/select.js
const app = getApp()
var userkey = null
Page({

  /**
   * 页面的初始数据
   */
  data: {
    pid: 0,
    cuint: '0.00',
    money: 10,
    pname: '--',
    siteimg: ''

  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    var pid = options.pid || app.GetUrlParam(unescape(options.q), "id");
    console.log(pid)
    var $this = this;
    if (pid == 0 || pid == undefined || pid == null) {
      wx.showModal({
        title: '消息提示',
        content: '设备不存在',
        showCancel: false,
        confirmText: '返回',
        success: function () {
          wx.navigateBack()
        }
      })
      return;
    }

    wx.showLoading({
      title: '加载中',
    })
    this.setData({ pid: pid})
    userkey = wx.getStorageSync("userkey")

  },

  /**
   * 生命周期函数--监听页面初次渲染完成
   */
  onReady: function () {
    if (userkey == null) { 
      userkey = wx.getStorageSync("userkey")
    }
    var $this=this;
    wx.request({
      url: app.globalData.URL + "/index.php?s=/Home/Index/getUint",
      data: { pid: $this.data.pid},
      method: "POST",
      header: app.globalData.header,
      success: function (res) {
        console.log(res.data)
        wx.hideLoading()
        if (res.data.status.err == 0) {
          wx.setNavigationBarTitle({
            title: res.data.sitename
          })
          $this.setData({
            pid: $this.data.pid,
            cuint: res.data.uint,
            pname: res.data.pname,
            siteimg: app.globalData.URL + res.data.siteimg
          })

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
      },
      fail: function () {
        wx.hideLoading()
      }
    })

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
   * 设置金额
   */
  moneyInput: function (e) {
    this.setData({
      money: e.detail.value
    })
  },
  /**
   * 充值请求
   */
  charge: function () {
    var $this = this;
    if (this.data.money == 0) {
      wx.showModal({
        title: '消息提示',
        content: '请输入充电金额',
        showCancel: false
      })
      return;
    }
    wx.showLoading({
      title: '启动中...',
      mask: true
    })
    if (userkey == null) {
      userkey = wx.getStorageSync("userkey")
    }

    var postdata = {
      uid: userkey.uid,
      sessionid: userkey.sessionid,
      pid: $this.data.pid,
      puint:parseFloat($this.data.cuint)*100,
      pmoney: parseFloat($this.data.money)*100
    }
    console.log(postdata)
    wx.request({
      url: app.globalData.URL + "/index.php?s=/Home/Index/getSign",
      data: postdata,
      method: "POST",
      header: app.globalData.header,
      success: function (res) {
        console.log(res.data)
        if (res.data.status.err == 0) {
          wx.hideLoading()
          $this.payinfo(res.data.data)
          //客户端充值，模拟数据跳过
          //$this.startPower(res.data.data)
        } else {
          wx.hideLoading()
          app.errAlert(res.data.status.msg)
        }
      },
      fail: function () {
        wx.hideLoading()
        app.errAlert('网络请求有误')
      }
    })
  },
  /**
   * 充值中
   */
  payinfo: function (pdata) {
    var $this = this;
    console.log(pdata)
    wx.requestPayment({
      'timeStamp': pdata.timestamp,
      'nonceStr': pdata.noncestr,
      'package': pdata.package,
      'signType': 'MD5',
      'paySign': pdata.paySign,
      'success': function (d) {
        $this.startPower(pdata.id)
      },
      'fail':function(res){
        app.errAlert(res.errMsg)
      }
    })
  },
  /**
    * 启动充电
    */
  startPower: function (_id) {
    if (userkey == null) {
      userkey = wx.getStorageSync("userkey")
    }
    var pdata={
      id:_id,
      uid: userkey.uid,
      sessionid: userkey.sessionid
    }
   
    console.log(pdata)
    var $this = this;
    wx.showLoading({
      title: '启动中...',
      mask: true
    })


    wx.request({
      url: app.globalData.URL + "/index.php?s=/Home/Index/startCharge",
      data: pdata,
      method: "POST",
      header: app.globalData.header,
      success: function (res) {
        console.log(res.data)
        if (res.data.status.err == 0) {
         // $this.getCharge(res.data.tid);
          wx.hideLoading()
          wx.navigateTo({
            url: "/pages/power/power?oid=" + res.data.tid
          })
        } else {
          wx.hideLoading()
          app.errAlert(res.data.status.msg)
        }

      },
      fail: function (res) {
        wx.hideLoading()
        app.errAlert('网络请求有误')
      }
    })
  }

})