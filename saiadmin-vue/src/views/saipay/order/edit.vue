<template>
  <component
    is="a-drawer"
    v-model:visible="visible"
    :title="title"
    :mask-closable="false"
    :ok-loading="loading"
    width="60%"
    @cancel="close"
    :footer="false">
    <div class="p-4">
      <div class="p-4">
        <a-steps :current="step">
          <a-step>创建订单</a-step>
          <a-step>支付通道</a-step>
          <a-step>支付页面</a-step>
        </a-steps>
      </div>
      <div class="p-4" v-if="step == 1">
        <!-- 表单信息 start -->
        <a-form ref="formRef" :model="formData" :rules="rules" :auto-label-width="true">
          <a-form-item label="订单名称" field="order_name">
            <a-input v-model="formData.order_name" placeholder="请输入订单名称" />
          </a-form-item>
          <a-form-item label="订单金额" field="order_price">
            <a-input-number
              v-model="formData.order_price"
              :step="1"
              :precision="2"
              :min="0.01"
              placeholder="请输入订单金额" />
          </a-form-item>
          <a-form-item label="订单备注" field="remark">
            <a-textarea v-model="formData.remark" placeholder="请输入订单备注" />
          </a-form-item>
        </a-form>
        <!-- 表单信息 end -->
        <div class="flex justify-center mt-4">
          <a-button type="primary" class="ml-4" :loading="loading" @click="handleCreate"> 提交订单 </a-button>
        </div>
      </div>
      <div class="p-4" v-if="step == 2">
        <div class="flex justify-center items-center min-h-60">
          <a-space direction="vertical" fill>
            <a-form-item label="支付金额">
              <span>￥{{ payInfo.order_price }}</span>
            </a-form-item>
            <a-form-item label="支付方式">
              <sa-radio v-model="payInfo.pay_method" dict="saipay_method" />
            </a-form-item>
          </a-space>
        </div>
        <div class="flex justify-center mt-4">
          <a-button type="primary" class="ml-4" :loading="loading" @click="handlePay"> 创建支付 </a-button>
        </div>
      </div>
      <div class="p-4" v-if="step == 3">
        <a-result :status="null">
          <template #icon>
            <IconFaceSmileFill :style="{ color: payInfo.pay_status == 1 ? '#ff0000' : '#888' }" />
          </template>
          <template #title>
            <sa-dict v-model="payOrder.pay_method" dict="saipay_method"></sa-dict>
            <span class="ml-4" v-if="payInfo.pay_status == 1">支付成功</span>
            <span class="ml-4" v-else>请扫描以下二维码进行支付</span>
          </template>
          <template #extra>
            <div class="flex justify-center">
              <img
                style="width: 180px; height: 180px"
                :src="'https://api.pwmqr.com/qrcode/create/?url=' + payOrder.code_url" />
            </div>
          </template>
          <a-typography style="background: var(--color-fill-2); padding: 16px">
            <a-typography-paragraph>温馨提示:</a-typography-paragraph>
            <ul>
              <li>二维码有效期为30分钟，请及时进行扫码支付。</li>
              <li>本商品一经售出，不支持申请退款。</li>
              <li>在支付过程中遇到问题，请联系客服。</li>
            </ul>
          </a-typography>
        </a-result>
      </div>
    </div>
  </component>
</template>

<script setup>
import { ref, reactive, computed, onUnmounted } from 'vue'
import { success, error } from '@/utils/common'
import api from '../api/order'

const emit = defineEmits(['success'])
// 引用定义
const visible = ref(false)
const loading = ref(false)
const formRef = ref()
const mode = ref('')
const step = ref(1)
const timer = ref(null)
const payInfo = reactive({
  id: null,
  pay_method: 1,
  pay_status: 2,
  order_price: null,
})
const payOrder = ref({})

let title = computed(() => {
  return '订单记录' + (mode.value == 'add' ? '-创建订单' : '-订单支付')
})

// 表单信息
const formData = reactive({
  id: null,
  order_no: '',
  order_name: '',
  order_price: null,
  pay_method: '',
  remark: '',
  pay_price: '0.00',
  pay_status: 2,
  order_status: 1,
})

// 验证规则
const rules = {
  order_name: [{ required: true, message: '订单名称必需填写' }],
  order_price: [{ required: true, message: '订单金额必需填写' }],
}

// 打开弹框
const open = async (type = 'add') => {
  mode.value = type
  if (type == 'add') {
    step.value = 1
  } else {
    step.value = 2
  }
  visible.value = true
  await initPage()
}

// 初始化页面数据
const initPage = async () => {
  formData.order_price = null
  formData.order_name = '测试订单' + Math.floor(Math.random() * 10000)
  formData.remark = ''

  payInfo.id = null
  payInfo.pay_method = 1
  payInfo.pay_status = 2
  payInfo.order_price = null
}

// 设置数据
const setFormData = async (data) => {
  for (const key in payInfo) {
    if (data[key] != null && data[key] != undefined) {
      payInfo[key] = data[key]
    }
  }
}

// 创建订单
const handleCreate = async () => {
  const validate = await formRef.value?.validate()
  if (!validate) {
    loading.value = true
    let data = { ...formData }
    let result = await api.save(data)
    if (result.code === 200) {
      success('创建订单成功')
      payInfo.id = result.data.id
      payInfo.order_price = result.data.order_price
      step.value = 2
    }
    loading.value = false
  }
}

// 创建支付订单
const handlePay = async () => {
  if (payInfo.pay_method == '') {
    error('请选择支付方式')
    return false
  }
  if (payInfo.id) {
    loading.value = true
    let data = { ...payInfo }
    let result = await api.pay(data)
    if (result.code === 200) {
      success('创建支付订单成功')
      step.value = 3
      payOrder.value = result.data
      timer.value = setInterval(checkOrder, 3000)
    }
    loading.value = false
  } else {
    error('请先创建订单')
  }
}

// 检查订单支付状态
const checkOrder = () => {
  api.checkPay({ order_no: payOrder.value.order_sn }).then((resp) => {
    if (resp.code === 200 && resp.data == true) {
      clearInterval(timer.value)
      payInfo.pay_status = 1
      emit('success')
    }
  })
}

onUnmounted(() => {
  clearInterval(timer.value)
})

// 关闭弹窗
const close = () => (visible.value = false)

defineExpose({ open, setFormData })
</script>
