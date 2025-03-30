<template>
  <component
    is="a-modal"
    v-model:visible="visible"
    :width="600"
    :title="title"
    :mask-closable="false"
    :ok-loading="loading"
    @cancel="close"
    @before-ok="submit">
    <!-- 表单信息 start -->
    <a-form ref="formRef" :model="formData" :rules="rules" :auto-label-width="true">
      <a-row :gutter="10">
        <a-col :span="24">
          <a-form-item label="网关" field="gateway">
            <a-select
              v-model="formData.gateway"
              :field-names="{ label: 'config_name', value: 'gateway' }"
              :options="optionData.gateway"
              placeholder="请选择网关"
              allow-clear
             />
          </a-form-item>
        </a-col>
        <a-col :span="24">
          <a-form-item label="手机号码" field="mobile">
            <a-input v-model="formData.mobile" placeholder="请输入手机号码" />
          </a-form-item>
        </a-col>
        <a-col :span="24">
          <a-form-item label="验证码" field="code">
            <a-input v-model="formData.code" placeholder="请输入验证码" />
          </a-form-item>
        </a-col>
        <a-col :span="24">
          <a-form-item label="短信内容" field="content">
            <a-input v-model="formData.content" placeholder="请输入短信内容" />
          </a-form-item>
        </a-col>
        <a-col :span="24">
          <a-form-item label="发送状态" field="status">
            <a-input v-model="formData.status" placeholder="请输入发送状态" />
          </a-form-item>
        </a-col>
        <a-col :span="24">
          <a-form-item label="返回结果" field="response">
            <a-input v-model="formData.response" placeholder="请输入返回结果" />
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
import api from '../api/record'

const emit = defineEmits(['success'])
// 引用定义
const visible = ref(false)
const loading = ref(false)
const formRef = ref()
const mode = ref('')
const optionData = reactive({
  gateway: [],
})

let title = computed(() => {
  return '短信记录' + (mode == 'add' ? '-新增' : '-编辑')
})

// 表单信息
const formData = reactive({
  id: null,
  gateway: '',
  mobile: '',
  code: '',
  content: '',
  status: '',
  response: '',
})

// 验证规则
const rules = {
  gateway: [{ required: true, message: '网关必需填写' }],
  mobile: [{ required: true, message: '手机号码必需填写' }],
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
  const resp_gateway = await commonApi.commonGet('/app/saisms/SmsConfig/index?saiType=all')
  optionData.gateway = resp_gateway.data
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
