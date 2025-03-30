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
          <a-form-item label="选择网关" field="gateway">
            <a-select
              v-model="formData.gateway"
              :field-names="{ label: 'config_name', value: 'gateway' }"
              :options="optionData.gateway"
              placeholder="请选择选择网关"
              allow-clear
             />
          </a-form-item>
        </a-col>
        <a-col :span="24">
          <a-form-item label="标签名称" field="tag_name">
            <a-input v-model="formData.tag_name" placeholder="请输入标签名称" />
          </a-form-item>
        </a-col>
        <a-col :span="24">
          <a-form-item label="使用模板" field="sms_type">
            <sa-radio v-model="formData.sms_type" dict="yes_or_no"/>
          </a-form-item>
        </a-col>
        <a-col :span="24">
          <a-form-item label="模板编号" field="template_id" v-if="formData.sms_type == 1">
            <a-input v-model="formData.template_id" placeholder="请输入模板编号" />
          </a-form-item>
        </a-col>
        <a-col :span="24">
          <a-form-item label="短信内容" field="content" v-if="formData.sms_type == 2">
            <a-textarea v-model="formData.content" placeholder="请输入短信内容" />
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
import api from '../api/tag'

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
  return '短信标签' + (mode == 'add' ? '-新增' : '-编辑')
})

// 表单信息
const formData = reactive({
  id: null,
  gateway: '',
  tag_name: '',
  sms_type: 1,
  content: '',
  template_id: '',
})

// 验证规则
const rules = {
  gateway: [{ required: true, message: '选择网关必需填写' }],
  tag_name: [{ required: true, message: '标签名称必需填写' }],
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
