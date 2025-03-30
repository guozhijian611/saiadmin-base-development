<template>
  <div class="ma-content-block">
    <sa-table ref="crudRef" :options="options" :columns="columns" :searchForm="searchForm">
      <!-- 搜索区 tableSearch -->
      <template #tableSearch>
        <a-col :span="8">
          <a-form-item label="账单号" field="order_sn">
            <a-input v-model="searchForm.order_sn" placeholder="请输入账单号" allow-clear />
          </a-form-item>
        </a-col>
        <a-col :span="8">
          <a-form-item label="交易单号" field="transaction_id">
            <a-input v-model="searchForm.transaction_id" placeholder="请输入交易单号" allow-clear />
          </a-form-item>
        </a-col>
        <a-col :span="8">
          <a-form-item label="支付状态" field="pay_status">
            <sa-select v-model="searchForm.pay_status" dict="saipay_pay" placeholder="请选择支付状态" allow-clear />
          </a-form-item>
        </a-col>
      </template>

      <!-- Table 自定义渲染 -->
      <template #order_sn="{ record }">
        <p>{{ record.create_time }}</p>
        <p>{{ record.order_sn }}</p>
      </template>
    </sa-table>
  </div>
</template>

<script setup>
import { onMounted, ref, reactive } from 'vue'
import { Message } from '@arco-design/web-vue'
import api from '../api/bill'

// 引用定义
const crudRef = ref()
const editRef = ref()

// 搜索表单
const searchForm = ref({
  order_sn: '',
  transaction_id: '',
  pay_status: '',
})

// SaTable 基础配置
const options = reactive({
  api: api.getPageList,
  recycleApi: api.getRecyclePageList,
  rowSelection: { showCheckedAll: true },
  delete: {
    show: true,
    auth: ['/app/saipay/Bill/destroy'],
    func: async (params) => {
      const resp = await api.delete(params)
      if (resp.code === 200) {
        Message.success(`删除成功！`)
        crudRef.value?.refresh()
      }
    },
    realAuth: ['/app/saipay/Bill/realDestroy'],
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
    auth: ['/app/saipay/Bill/recovery'],
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
  { title: '账单号|创建时间', dataIndex: 'order_sn', width: 180 },
  { title: '账单信息', dataIndex: 'message', width: 180 },
  { title: '账单金额', dataIndex: 'money', width: 100 },
  {
    title: '支付方式',
    dataIndex: 'pay_method',
    dict: 'saipay_method',
    colors: ['#168cff', '#00b42a', '#ff7d00', '#f53f3f'],
    width: 100,
  },
  { title: '支付状态', dataIndex: 'pay_status', dict: 'saipay_pay', width: 100 },
  { title: '交易订单', dataIndex: 'transaction_id', width: 180 },
  { title: '备注', dataIndex: 'extra', width: 120 },
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
