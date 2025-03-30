import { request } from '@/utils/request.js'

/**
 * 订单记录 API接口
 */
export default {
  /**
   * 数据列表
   * @returns
   */
  getPageList(params = {}) {
    return request({
      url: '/app/saipay/Order/index',
      method: 'get',
      params
    })
  },

  /**
   * 回收站数据列表
   * @returns
   */
  getRecyclePageList(params = {}) {
    return request({
      url: '/app/saipay/Order/recycle',
      method: 'get',
      params
    })
  },
  /**
   * 添加数据
   * @returns
   */
  save(params = {}) {
    return request({
      url: '/app/saipay/Order/save',
      method: 'post',
      data: params
    })
  },

  /**
   * 读取数据
   * @returns
   */
  read(id) {
    return request({
      url: '/app/saipay/Order/read?id=' + id,
      method: 'get'
    })
  },

  /**
   * 软删除数据
   * @returns
   */
  delete(data) {
    return request({
      url: '/app/saipay/Order/destroy',
      method: 'delete',
      data
    })
  },

  /**
   * 恢复回收站数据
   * @returns
   */
  recovery(data) {
    return request({
      url: '/app/saipay/Order/recovery',
      method: 'post',
      data
    })
  },

  /**
   * 真实删除数据
   * @returns
   */
  realDestroy(data) {
    return request({
      url: '/app/saipay/Order/realDestroy',
      method: 'delete',
      data
    })
  },
  /**
   * 更新数据
   * @returns
   */
  update(id, data = {}) {
    return request({
      url: '/app/saipay/Order/update?id=' + id,
      method: 'put',
      data
    })
  },

  /**
   * 更改状态
   * @returns
   */
  changeStatus(data = {}) {
    return request({
      url: '/app/saipay/Order/changeStatus',
      method: 'post',
      data
    })
  },

  /**
   * 创建支付订单
   * @returns
   */
  pay(data = {}) {
    return request({
      url: '/app/saipay/Order/pay',
      method: 'post',
      data
    })
  },

  /**
   * 检查支付状态
   * @returns
   */
  checkPay(data = {}) {
    return request({
      url: '/app/saipay/Notify/checkOrder',
      method: 'post',
      data
    })
  }
}
