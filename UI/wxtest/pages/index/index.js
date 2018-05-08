//index.js
//获取应用实例
const app = getApp()
Page({

  /**
   * 页面的初始数据
   */
  data: {
    hasLocation: false,
    csiteid: -1,
    location: {},
    markers: null,
    distan: 0,
    controls: null,
    display: "none",
    tel: ""
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {

    var wh = wx.getSystemInfoSync()
    var kScreenW = wh.windowWidth / 375
    var kScreenH = wh.windowHeight / 603
   
    //地图按钮
    var controls = [{
      id: 1,
      iconPath: '/resources/btn_saoma@2x.png',
      position: {
        left: 125 * kScreenW,
        top: 523 * kScreenH,
        width: 125 * kScreenW,
        height: 40 * kScreenW
      },
      clickable: true
    },
    {
      id: 2,
      iconPath: '/resources/btn_local@2x.png',
      position: {
        left: 10 * kScreenW,
        top: 523 * kScreenH,
        width: 40 * kScreenH,
        height: 40 * kScreenH
      },
      clickable: true
    },
    {
      id: 3,
      iconPath: '/resources/btn_kefu@2x.png',
      position: {
        left: 325 * kScreenW,
        top: 523 * kScreenH,
        width: 40 * kScreenH,
        height: 40 * kScreenH
      },
      clickable: true
    },
    {
      id: 4,
      iconPath: '/resources/btn_list@2x.png',
      position: {
        left: 325 * kScreenW,
        top: 473 * kScreenH,
        width: 40 * kScreenH,
        height: 40 * kScreenH
      },
      clickable: true
    },
    {
      id: 5,
      iconPath: '/resources/btn_user@2x.png',
      position: {
        left: 325 * kScreenW,
        top: 423 * kScreenH,
        width: 40 * kScreenH,
        height: 40 * kScreenH
      },
      clickable: true
    }
    ]
    var $this = this;
    this.setData({
      controls: controls, 
      location: {
        longitude: app.globalData.loacal.longitude,
        latitude: app.globalData.loacal.latitude
      } 
    });
    //mark数据
    wx.request({
      url: app.globalData.URL + '/index.php?s=/Home/Index/Site_list',
      success: function (res) {
        $this.setData({ markers: res.data.site, tel: res.data.tel })
        app.globalData.siteArr = res.data.site
      }
    })

    //当前位置
    wx.getLocation({
      success: function (res) {
        $this.setData({
          hasLocation: true,
          location: {
            longitude: res.longitude,
            latitude: res.latitude
          }
        })
        app.globalData.loacal = {
          longitude: res.longitude,
          latitude: res.latitude
        }
      }
    })
    wx.hideLoading()
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
   * 用户点击control
   */
  controltap: function (e) {
    var $this = this;
    console.log(e.controlId)
    switch (e.controlId) {
      case 1:
        $this.openScan();
        break;
      case 2:
        wx.createMapContext("map").moveToLocation()
        break;
      case 3:
        wx.showActionSheet({
          itemList: ['联系客服', '权限设置'],
          success: function (res) {
            console.log(res.tapIndex)
            if (res.tapIndex == 0) {
              $this.Connert()
            } else if (res.tapIndex == 1) {
              wx.openSetting({
                success: (res) => {
                },
                fail:(res)=>{
                  app.errAlert('打开权限设置失败')
                }
              })
            }
          }
        })
        break;
      case 4:
        wx.navigateTo({ url: '/pages/list/list' })
        break;
      case 5:
        wx.navigateTo({ url: "/pages/userlist/userlist" })
        break;
    }
  },
  /**
   * 用户点击地图mark，显示详情
   */
  markertap: function (e) {
    var csiteid = e.markerId
    var marksicon = "markers[" + csiteid + "].iconPath";
    var distan = app.getFlatternDistance(this.data.location.latitude, this.data.location.longitude, this.data.markers[csiteid].latitude, this.data.markers[csiteid].longitude);

    this.setData({ [marksicon]: "/resources/marker_cur@2x.png", csiteid: csiteid, display: "block", distan: distan });
  },
  /**
  * 用户点击地图半透明背景隐藏详情
  */
  clkmask: function () {
    var csiteid = this.data.csiteid
    var marksicon = "markers[" + csiteid + "].iconPath";
    this.setData({ [marksicon]: "/resources/marker@2x.png", csiteid: -1, display: "none" });
  },
  /**
   * 用户点击详情右侧车位按钮
   */
  clk_car: function () {
    var csiteid = this.data.csiteid
    var sid = this.data.markers[csiteid].sid;

    wx.navigateTo({ url: '/pages/map/map?sid=' + sid })
  },
  /**
    * 用户点击详情右侧详情按钮
  */
  clk_info: function () {
    var csiteid = this.data.csiteid
    var sid = this.data.markers[csiteid].sid;
    wx.navigateTo({ url: '/pages/info/info?sid=' + sid })
  },
  /**
   * 点击扫码充电
 */
  openScan: function () {
    var $this = this;
    wx.showLoading({
      title: '加载中',
      mask: true
    })
    if (app.globalData.userkey==null){
      app.errAlert("正在确认您的身份，请稍后"); 
      return;
    }

    //发送后台判断用户是否有信息
    wx.request({
      url: app.globalData.URL + "/index.php?s=/Home/Index/getPowerNum",
      data: {
        uid: app.globalData.userkey.uid,
        sessionid: app.globalData.userkey.sessionid
      },
      method: "POST",
      header: app.globalData.header,
      success: function (res) {
        wx.hideLoading()
        console.log(res.data)
        if (res.data.status.err == 0) {
          if (res.data.count == 0) { //无充电记录直接开启扫码
            $this.scanCode();
          } else { //有充电记录弹框
            var item = [];
            for (var i = 0; i < res.data.data.length; i++) {
              item.push('查看订单 ' + res.data.data[i].No)
            }
            item.push('继续扫码充电');
            wx.showActionSheet({
              itemList: item,
              success: function (res1) {
                console.log(res1.tapIndex)
                if (res1.tapIndex < res.data.data.length) {
                  wx.navigateTo({
                    url: "/pages/power/power?oid=" + res.data.data[res1.tapIndex].id
                  })
                } else if (res1.tapIndex == res.data.data.length) {
                  $this.scanCode();
                }
              }
            })
          }
        } else {
          app.errAlert(res.data.status.msg)
        }

      }, fail: function () {
        wx.hideLoading()
        app.errAlert('数据请求有误')
      }
    })

  },
  /**
   * 统一扫码方法
 */
  scanCode: function () {
    wx.scanCode({
      onlyFromCamera: true,
      success: (res) => {
        console.log(res)
        if (res.result.indexOf('https://v.vmuui.com/power?pid=') == 0) {
          wx.navigateTo({
            url: "/pages/select/select?pid=" + app.GetUrlParam(res.result, "pid") + "&pNo=" + app.GetUrlParam(res.result, "pNo")
          })
        } else {
          app.errAlert('二维码无法识别')
        }
      }
    })
  },
  /**
   * 联系客服
   */
  Connert: function () {

    var tel = this.data.tel
    if (tel == "") {
      app.errAlert('未设置电话')
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
  }

})