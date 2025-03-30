<template>
  <div class="ma-content-block">
    <sa-table ref="crudRef" :options="options" :columns="columns" :searchForm="searchForm">
      <!-- 搜索区 tableSearch -->
      <template #tableSearch>
        <a-col :span="8">
          <a-form-item label="订单名称" field="order_name">
            <a-input v-model="searchForm.order_name" placeholder="请输入订单名称" allow-clear />
          </a-form-item>
        </a-col>
        <a-col :span="8">
          <a-form-item label="订单号" field="order_no">
            <a-input v-model="searchForm.order_no" placeholder="请输入订单号" allow-clear />
          </a-form-item>
        </a-col>
        <a-col :span="8">
          <a-form-item label="支付方式" field="pay_method">
            <sa-select v-model="searchForm.pay_method" dict="saipay_method" placeholder="请选择支付方式" allow-clear />
          </a-form-item>
        </a-col>
        <a-col :span="8">
          <a-form-item label="订单状态" field="order_status">
            <sa-select
              v-model="searchForm.order_status"
              dict="saipay_status"
              placeholder="请选择订单状态"
              allow-clear />
          </a-form-item>
        </a-col>
        <a-col :span="16">
          <a-form-item field="create_time" label="下单时间">
            <a-range-picker v-model="searchForm.create_time" showTime style="width: 100%" />
          </a-form-item>
        </a-col>
      </template>

      <!-- Table 自定义渲染 -->
      <template #order_no="{ record }">
        <p>{{ record.create_time }}</p>
        <p>{{ record.order_no }}</p>
      </template>
      <!-- 操作列 -->
      <template #operationBeforeExtend="{ record }">
        <a-link type="primary" v-if="record.pay_status == 2" @click="handlePay(record)"> <icon-export />支付 </a-link>
      </template>
    </sa-table>

    <!-- 编辑 -->
    <edit-form ref="editRef" @success="refresh" />
  </div>
</template>

<script setup>
import { onMounted, ref, reactive } from 'vue'
import { Message } from '@arco-design/web-vue'
import EditForm from './edit.vue'
import api from '../api/order'

// 引用定义
const crudRef = ref()
const editRef = ref()

// 搜索表单
const searchForm = ref({
  order_no: '',
  order_name: '',
  pay_method: '',
  order_status: '',
  create_time: null,
})

const handlePay = async (record) => {
  editRef.value?.open('edit')
  editRef.value?.setFormData(record)
}

// SaTable 基础配置
const options = reactive({
  api: api.getPageList,
  recycleApi: api.getRecyclePageList,
  rowSelection: { showCheckedAll: true },
  add: {
    show: true,
    text: '创建订单',
    auth: ['/app/saipay/Order/save'],
    func: async () => {
      editRef.value?.open()
    },
  },
  delete: {
    show: true,
    auth: ['/app/saipay/Order/destroy'],
    func: async (params) => {
      const resp = await api.delete(params)
      if (resp.code === 200) {
        Message.success(`删除成功！`)
        crudRef.value?.refresh()
      }
    },
    realAuth: ['/app/saipay/Order/realDestroy'],
    realFunc: async (params) => {
      const resp = await api.realDestroy(params)
      if (resp.code === 200) {
        Message.success(`销毁成功！`)
        crudRef.value?.refresh()
      }
    },
  },
  recovery: {
    show: true,
    auth: ['/app/saipay/Order/recovery'],
    func: async (params) => {
      const resp = await api.recovery(params)
      if (resp.code === 200) {
        Message.success(`恢复成功！`)
        crudRef.value?.refresh()
      }
    },
  },
})

// SaTable 列配置
const columns = reactive([
  { title: '下单时间|订单号', dataIndex: 'order_no', width: 180 },
  { title: '订单名称', dataIndex: 'order_name', width: 250 },
  { title: '订单金额', dataIndex: 'order_price', width: 100 },
  { title: '支付金额', dataIndex: 'pay_price', width: 100 },
  {
    title: '支付方式',
    dataIndex: 'pay_method',
    dict: 'saipay_method',
    colors: ['#168cff', '#00b42a', '#ff7d00', '#f53f3f'],
    width: 100,
  },
  { title: '支付状态', dataIndex: 'pay_status', dict: 'saipay_pay', width: 100 },
  { title: '订单状态', dataIndex: 'order_status', dict: 'saipay_status', width: 120 },
])

// 页面数据初始化
const initPage = async () => {}

// SaTable 数据请求
const refresh = async () => {
  crudRef.value?.refresh()
}

// 页面加载完成执行
onMounted(async () => {
  initPage()
  refresh()
})
</script>
