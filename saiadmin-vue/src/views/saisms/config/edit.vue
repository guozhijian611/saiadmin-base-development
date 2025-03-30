<template>
  <component
    is="a-modal"
    v-model:visible="visible"
    :width="800"
    :title="title"
    :mask-closable="false"
    :ok-loading="loading"
    @cancel="close"
    @before-ok="submit">
    <!-- 表单信息 start -->
    <a-form ref="formRef" :model="formData" :rules="rules" :auto-label-width="true">
      <a-row :gutter="10">
        <a-col :span="24">
          <a-form-item label="网关标识" field="gateway">
            <a-input v-model="formData.gateway" placeholder="请输入网关标识" />
          </a-form-item>
        </a-col>
        <a-col :span="24">
          <a-form-item label="网关名称" field="config_name">
            <a-input v-model="formData.config_name" placeholder="请输入网关名称" />
          </a-form-item>
        </a-col>
        <a-col :span="24">
          <a-form-item label="配置" field="config">
            <ma-codeEditor v-model="formData.config" :height="300" />
          </a-form-item>
        </a-col>
        <a-col :span="24">
          <a-form-item label="排序" field="sort">
            <a-input-number v-model="formData.sort" placeholder="请输入排序" />
          </a-form-item>
        </a-col>
        <a-col :span="24">
          <a-form-item label="状态" field="status">
            <sa-radio v-model="formData.status" dict="data_status" />
          </a-form-item>
        </a-col>
      </a-row>
    </a-form>
    <!-- 表单信息 end -->
  </component>
</template>

<script setup>
import { ref, reactive, computed } from 'vue'
import { Message, Modal } from '@arco-design/web-vue'
import commonApi from '@/api/common'
import api from '../api/config'

const emit = defineEmits(['success'])
// 引用定义
const visible = ref(false)
const loading = ref(false)
const formRef = ref()
const mode = ref('')
const optionData = reactive({
})

let title = computed(() => {
  return '短信配置' + (mode == 'add' ? '-新增' : '-编辑')
})

// 表单信息
const formData = reactive({
  id: null,
  gateway: '',
  config_name: '',
  config: '',
  sort: 100,
  status: 1,
})

// 验证规则
const rules = {
  gateway: [{ required: true, message: '网关标识必需填写' }],
  config_name: [{ required: true, message: '网关名称必需填写' }],
}

// 打开弹框
const open = async (type = 'add') => {
  mode.value = type
  visible.value = true
  formRef.value.resetFields()
  await initPage()
}

// 初始化页面数据
const initPage = async () => {
}

// 设置数据
const setFormData = async (data) => {
  for (const key in formData) {
    if (data[key] != null && data[key] != undefined) {
      formData[key] = data[key]
    }
  }
}

// 数据保存
const submit = async (done) => {
  const validate = await formRef.value?.validate()
  if (!validate) {
    loading.value = true
    let data = { ...formData }
    if (data.config && typeof data.config === 'string') {
        data.config = JSON.parse(data.config)
    }
    let result = {}
    if (mode.value === 'add') {
      // 添加数据
      data.id = undefined
      result = await api.save(data)
    } else {
      // 修改数据
      result = await api.update(data.id, data)
    }
    if (result.code === 200) {
      Message.success('操作成功')
      emit('success')
      done(true)
    }
    // 防止连续点击提交
    setTimeout(() => {
      loading.value = false
    }, 500)
  }
  done(false)
}

// 关闭弹窗
const close = () => (visible.value = false)

defineExpose({ open, setFormData })
</script>
