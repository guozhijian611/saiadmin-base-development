<template>
  <div class="ma-content-block">
    <sa-table ref="crudRef" :options="options" :columns="columns" :searchForm="searchForm">
      <!-- 搜索区 tableSearch -->
      <template #tableSearch>
        <a-col :span="8">
          <a-form-item label="网关标识" field="gateway">
            <a-input v-model="searchForm.gateway" placeholder="请输入网关标识" />
          </a-form-item>
        </a-col>
        <a-col :span="8">
          <a-form-item label="网关名称" field="config_name">
            <a-input v-model="searchForm.config_name" placeholder="请输入网关名称" />
          </a-form-item>
        </a-col>
      </template>

      <!-- Table 自定义渲染 -->
      <template #status="{ record }">
        <sa-switch v-model="record.status" @change="changeStatus($event, record.id)"></sa-switch>
      </template>
    </sa-table>

    <!-- 编辑表单 -->
    <edit-form ref="editRef" @success="refresh" />
  </div>
</template>

<script setup>
import { onMounted, ref, reactive } from 'vue'
import { Message } from '@arco-design/web-vue'
import EditForm from './edit.vue'
import api from '../api/config'

// 引用定义
const crudRef = ref()
const editRef = ref()

const changeStatus = async (status, id) => {
  const resp = await api.changeStatus({ id, status })
  if (resp.code === 200) {
    Message.success(resp.message)
    crudRef.value?.refresh()
  }
}

// 搜索表单
const searchForm = ref({
  gateway: '',
  config_name: '',
  orderBy: 'sort',
  orderType: 'desc',
})


// SaTable 基础配置
const options = reactive({
  api: api.getPageList,
  recycleApi: api.getRecyclePageList,
  rowSelection: { showCheckedAll: true },
  singleLine: true,
  add: {
    show: true,
    auth: ['/app/saisms/SmsConfig/save'],
    func: async () => {
      editRef.value?.open()
    },
  },
  edit: {
    show: true,
    auth: ['/app/saisms/SmsConfig/update'],
    func: async (record) => {
      editRef.value?.open('edit')
      editRef.value?.setFormData(record)
    },
  },
  delete: {
    show: true,
    auth: ['/app/saisms/SmsConfig/destroy'],
    func: async (params) => {
      const resp = await api.delete(params)
      if (resp.code === 200) {
        Message.success(`删除成功！`)
        crudRef.value?.refresh()
      }
    },
    realAuth: ['/app/saisms/SmsConfig/realDestroy'],
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
    auth: ['/app/saisms/SmsConfig/recovery'],
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
  { title: '排序', dataIndex: 'sort', width: 120 },
  { title: '网关标识', dataIndex: 'gateway', width: 180 },
  { title: '网关名称', dataIndex: 'config_name', width: 180 },
  { title: '状态', dataIndex: 'status', dict: 'data_status', width: 180 },
])

// 页面数据初始化
const initPage = async () => {
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
