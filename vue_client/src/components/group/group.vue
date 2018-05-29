<style>

</style>
<template>
  <div>
    <panel :list="list" :type="type"></panel>
  </div>
</template>
<script>
import { Radio,Group,Panel } from 'vux'
export default {
  data() {
    return {
      type: '3',
      list: [{
        src: '/static/image/ironcat.jpg',
        title: '群聊一',
        desc: '由各种物质组成的巨型球状天体，叫做星球。星球有一定的形状，有自己的运行轨道。',
        url: '/chatroom'
      }, {
        src: '/static/image/ironcat.jpg',
        title: '群聊二',
        desc: '由各种物质组成的巨型球状天体，叫做星球。星球有一定的形状，有自己的运行轨道。',
        url: {
          path: '/chatroom',
          replace: false
        },
        meta: {
          source: '来源信息',
          date: '时间',
          other: '其他信息'
        }
      }],
      footer: {
        title: '111',
        url: 'http://vux.li'
      }
    }
  },
  components: {
    Radio,
    Group,
    Panel 
  }
}
</script>
<style>

</style>

