<template>
    <div style="height:100%;">
        <x-header :left-options="{showBack: false}"  style="width:100%;position:absolute;left:0;top:0;z-index:100;">布局文件</x-header>
        <router-link to=""></router-link>
        <tabbar>
            <tabbar-item>
                <img slot="icon" src="">
                <span slot="label">Wechat</span>
            </tabbar-item>
            <tabbar-item show-dot>
                <img slot="icon" src="">
                <span slot="label">Message</span>
            </tabbar-item>
            <tabbar-item selected link="">
                <img slot="icon" src="">
                <span slot="label">Explore</span>
            </tabbar-item>
            <tabbar-item badge="2">
                <img slot="icon" src="">
                <span slot="label">News</span>
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
  components: {
    Tabbar,
    TabbarItem,
    XHeader,
    ViewBox
  }
}
</script>
<style>

</style>
