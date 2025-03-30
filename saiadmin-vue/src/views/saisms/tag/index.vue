<template>
  <div class="ma-content-block">
    <sa-table ref="crudRef" :options="options" :columns="columns" :searchForm="searchForm">
      <!-- 搜索区 tableSearch -->
      <template #tableSearch>
        <a-col :span="8">
          <a-form-item label="标签名称" field="tag_name">
            <a-input v-model="searchForm.tag_name" placeholder="请输入标签名称" />
          </a-form-item>
        </a-col>
      </template>
      <!-- 操作后置扩展 -->
      <template #operationBeforeExtend="{ record }">
        <a-link @click="handleTest(record)"><icon-export /> 测试</a-link>
      </template>
    </sa-table>

    <!-- 编辑表单 -->
    <edit-form ref="editRef" @success="refresh" />
    <!-- 测试表单 -->
    <test-form ref="testRef" />
  </div>
</template>

<script setup>
import { onMounted, ref, reactive } from 'vue'
import { Message } from '@arco-design/web-vue'
import commonApi from '@/api/common'
import EditForm from './edit.vue'
import TestForm from './test.vue'
import api from '../api/tag'

// 引用定义
const crudRef = ref()
const editRef = ref()
const testRef = ref()
const optionData = reactive({
  gateway: [],
})

// 搜索表单
const searchForm = ref({
  tag_name: '',
})

const handleTest = async (record) => {
  testRef.value?.open()
  testRef.value?.setFormData(record)
}

// SaTable 基础配置
const options = reactive({
  api: api.getPageList,
  recycleApi: api.getRecyclePageList,
  rowSelection: { showCheckedAll: true },
  operationColumnWidth: 200,
  add: {
    show: true,
    auth: ['/app/saisms/SmsTag/save'],
    func: async () => {
      editRef.value?.open()
    },
  },
  edit: {
    show: true,
    auth: ['/app/saisms/SmsTag/update'],
    func: async (record) => {
      editRef.value?.open('edit')
      editRef.value?.setFormData(record)
    },
  },
  delete: {
    show: true,
    auth: ['/app/saisms/SmsTag/destroy'],
    func: async (params) => {
      const resp = await api.delete(params)
      if (resp.code === 200) {
        Message.success(`删除成功！`)
        crudRef.value?.refresh()
      }
    },
    realAuth: ['/app/saisms/SmsTag/realDestroy'],
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
    auth: ['/app/saisms/SmsTag/recovery'],
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
  { title: '标签名称', dataIndex: 'tag_name', width: 180 },
  { title: '选择网关', dataIndex: 'gateway', width: 180 },
  { title: '模板编号', dataIndex: 'template_id', width: 180 },
  { title: '发送内容', dataIndex: 'content' },
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
