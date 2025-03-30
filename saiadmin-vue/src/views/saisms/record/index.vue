<template>
  <div class="ma-content-block">
    <sa-table ref="crudRef" :options="options" :columns="columns" :searchForm="searchForm">
      <!-- 搜索区 tableSearch -->
      <template #tableSearch>
        <a-col :span="8">
          <a-form-item label="网关" field="gateway">
            <a-select
              v-model="searchForm.gateway"
              :field-names="{ label: 'config_name', value: 'gateway' }"
              :options="optionData.gateway"
              placeholder="请选择网关"
              allow-clear
            />
          </a-form-item>
        </a-col>
        <a-col :span="8">
          <a-form-item label="发送状态" field="status">
            <a-select
              v-model="searchForm.status"
              :field-names="{ label: 'label', value: 'value' }"
              :options="optionData.status"
              placeholder="请选择发送状态"
              allow-clear
            />
          </a-form-item>
        </a-col>
        <a-col :span="8">
          <a-form-item label="手机号码" field="mobile">
            <a-input v-model="searchForm.mobile" placeholder="请输入手机号码" />
          </a-form-item>
        </a-col>
        <a-col :span="16">
          <a-form-item field="create_time" label="发送时间">
            <a-range-picker v-model="searchForm.create_time" showTime style="width: 100%" />
          </a-form-item>
        </a-col>
      </template>

      <!-- Table 自定义渲染 -->
      <template #status="{ record }">
        <a-tag color="green" v-if="record.status === 'success'">成功</a-tag>
        <a-tag color="red" v-if="record.status === 'failure'">失败</a-tag>
        <a-tag color="orange" v-if="record.status === 'unsend'">调用失败</a-tag>
      </template>
    </sa-table>

    <!-- 编辑表单 -->
    <edit-form ref="editRef" @success="refresh" />
  </div>
</template>

<script setup>
import { onMounted, ref, reactive } from 'vue'
import { Message } from '@arco-design/web-vue'
import commonApi from '@/api/common'
import EditForm from './edit.vue'
import api from '../api/record'

// 引用定义
const crudRef = ref()
const editRef = ref()
const optionData = reactive({
  gateway: [],
  status: [
    { label: '成功', value: 'success' },
    { label: '失败', value: 'failure' },
    { label: '调用失败', value: 'unsend' },
  ],
})

// 搜索表单
const searchForm = ref({
  gateway: '',
  mobile: '',
  status: '',
  create_time: [],
  orderBy: 'create_time',
  orderType: 'desc',
})


// SaTable 基础配置
const options = reactive({
  api: api.getPageList,
  rowSelection: { showCheckedAll: true },
  delete: {
    show: true,
    auth: ['/app/saisms/SmsRecord/destroy'],
    func: async (params) => {
      const resp = await api.delete(params)
      if (resp.code === 200) {
        Message.success(`删除成功！`)
        crudRef.value?.refresh()
      }
    },
  },
})

// SaTable 列配置
const columns = reactive([
  { title: '发送时间', dataIndex: 'create_time', width: 180 },
  { title: '网关', dataIndex: 'gateway', width: 100 },
  { title: '手机号码', dataIndex: 'mobile', width: 140 },
  { title: '验证码', dataIndex: 'code', width: 100 },
  { title: '发送状态', dataIndex: 'status', width: 100 },
  { title: '返回结果', dataIndex: 'response' },
])

// 页面数据初始化
const initPage = async () => {
  const resp_gateway = await commonApi.commonGet('/app/saisms/SmsConfig/index?saiType=all')
  optionData.gateway = resp_gateway.data
}

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
