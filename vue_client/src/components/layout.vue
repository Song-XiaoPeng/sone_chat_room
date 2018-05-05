<style scoped>
    .content {
        margin-top: 46px;
    }
</style>
<template>
    <div style="height:100%;">
        <x-header :left-options="{showBack}" @on-click-back="" style="width:100%;position:absolute;left:0;top:0;z-index:100;">三人行必有我师焉</x-header>
        <div class="content">
            <router-view></router-view>
        </div>
        <tabbar>
            <tabbar-item link="/home/group" @on-item-click="">
                <i class="fa fa-comment" slot="icon"></i>
                <span slot="label">群聊分组</span>
            </tabbar-item>
            <tabbar-item show-dot link="/home/friends" @on-item-click="">
                <i class="fa fa-handshake-o" slot="icon"></i>
                <span slot="label">好友列表</span>
            </tabbar-item>
            <tabbar-item selected link="/home/interest" @on-item-click="">
                <i class="fa fa-telegram" slot="icon"></i>
                <span slot="label">Explore</span>
            </tabbar-item>
            <tabbar-item badge="2" link="/home/profile" @on-item-click="">
                <i class="fa fa-user-o" slot="icon"></i>
                <span slot="label">个人中心</span>
            </tabbar-item>
        </tabbar>
    </div>
</template>
<script>
import { Tabbar, TabbarItem, XHeader, ViewBox } from 'vux'

export default {
  data() {
      return {
          showBack: false
      }
  },
  methods: {
      showBackIcon(val) {
          this.showBack = val
          console.log(this.showBack)

      }
  },
  components: {
    Tabbar,
    TabbarItem,
    XHeader,
    ViewBox
  }
}
</script>

