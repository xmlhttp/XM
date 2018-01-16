// pages/power/power.js
const app = getApp()
var cstime = 0;
var st = null;
var oid = 0;
Page({

  /**
   * 页面的初始数据
   */
  data: {
    pname: '加载中',
    siteimg: '',
    cuint: '0.00',
    cmoney: '0.00',
    ctime: '00 小时 00 分 00 秒',
    cele: 0.0,
    statu: '充电中',
    color: '#0c0'
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    oid = options.oid;
    console.log(oid)
    var $this = this;
    if (oid == 0 || oid == undefined || oid == null) {
      wx.showModal({
        title: '消息提示',
        content: '设备不存在',
        confirmText: '返回',
        success: function (res) {
          if (res.confirm) {
            wx.navigateBack()
          }
        }
      })
      return;
    }
    if (app.globalData.userkey == null) {
      app.errAlert("身份认证有误，请退出程序在重试！");
      return;
    }
    wx.request({
      url: app.globalData.URL + "/index.php?s=/Home/Index/getPower",
      data: {
        oid: oid,
        uid: app.globalData.userkey.uid,
        sessionid: app.globalData.userkey.sessionid
      },
      method: "POST",
      header: app.globalData.header,
      success: function (res) {
        console.log(res.data)
        if (res.data.status.err == 0) {
          wx.setNavigationBarTitle({
            title: res.data.sitename + "-充电信息"
          })
          cstime = res.data.ctime
          var time = $this.strTotime(res.data.ctime)
          $this.setData({
            pname: res.data.pname,
            siteimg: app.globalData.URL + res.data.siteimg,
            cuint: res.data.uint,
            cmoney: res.data.smoney,
            ctime: time,
            cele: res.data.cele,
            color: res.data.color,
            statu: res.data.statu
          })
          st = setTimeout(function () {
            cstime++
            $this.showtime()
          }, 1000)

        } else {
          wx.showModal({
            title: '消息提示',
            content: res.data.status.msg,
            showCancel: false,
            confirmText: '返回',
            success: function (res) {
              if (res.confirm) {
                wx.navigateBack()
              }
            }
          })
        }
      },
      fail: function () {
        wx.showModal({
          title: '消息提示',
          content: '请求有误',
          showCancel: false,
          confirmText: '返回',
          success: function (res) {
            if (res.confirm) {
              wx.navigateBack()
            }
          }
        })
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
    clearTimeout(st)
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
  * 数据显示函数
  */






  /**
  * 字符串转换时间
  */
  strTotime: function (str) {
    var timeh = parseInt(parseInt(str) / 3600)
    var timeh1 = parseInt(parseInt(str) % 3600)
    var timeint = parseInt(timeh1 / 60)
    var timepart = parseInt(timeh1 % 60)
    return (timeh < 10 ? ("0" + timeh) : timeh) + " 时 " + (timeint < 10 ? ("0" + timeint) : timeint) + " 分 " + (timepart < 10 ? ("0" + timepart) : timepart) + " 秒"
  },
  /**
 * 定时显示
 */
  showtime: function () {

    var $this = this
    var time = $this.strTotime(cstime)
    $this.setData({
      ctime: time
    })
    st = setTimeout(function () {
      cstime++
      $this.showtime()
    }, 1000)
  },
  /***
   * 停止充电
   */
  stopcharge: function () {
    if (oid == 0 || oid == undefined || oid == null) {
      wx.showModal({
        title: '消息提示',
        content: '设备不存在',
        confirmText: '返回',
        success: function (res) {
          if (res.confirm) {
            wx.navigateBack()
          }
        }
      })
      return;
    }

    if (app.globalData.userkey == null) {
      wx.showModal({
        title: '消息提示',
        content: '身份认证有误，请退出程序重进！',
        confirmText: '返回',
        success: function (res) {
          if (res.confirm) {
            wx.navigateBack()
          }
        }
      })
      return;
    }

    wx.showLoading({
      title: '停止中',
      mask:true
    })
    wx.request({
      url: app.globalData.URL + "/index.php?s=/Home/Index/stopCharge",
      data: {
        id: oid,
        uid: app.globalData.userkey.uid,
        sessionid: app.globalData.userkey.sessionid
      },
      method: "POST",
      header: app.globalData.header,
      success: function (res) {
        console.log(res.data)
        if (res.data.status.err == 0) {
          clearTimeout(st)
        }
        wx.hideLoading()
        wx.showModal({
          title: '消息提示',
          content: res.data.status.msg,
          showCancel: false,
          confirmText: '确定'
        })
      },
      fail: function () {
        wx.hideLoading()
        wx.showModal({
          title: '消息提示',
          content: '网络错误',
          showCancel: false,
          confirmText: '确定'
        })
      }
    })
  }
})