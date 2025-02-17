import { defineStore } from 'pinia';
import { updateBodyClass,updateSelectorClass,updateColorRootVar} from '../utilities/setting'
import { setFontFamily } from '../utilities/root-var'
import { initialState as state, defaultState } from './state'

const DefaultSetting = defaultState.setting

const Choices = {
  CardStyle: DefaultSetting.card_style.choices,
  FooterStyle: DefaultSetting.footer_style.choices,
  ColorChoice: DefaultSetting.theme_color.choices,
  HeaderNavbarStyle: DefaultSetting.header_navbar.choices,
  MenuColor: DefaultSetting.sidebar_color.choices,
  SidebarMenuStyle: DefaultSetting.sidebar_menu_style.choices,
  SidebarShow: DefaultSetting.sidebar_show.choices,
  NavbarShow: DefaultSetting.navbar_show.choices,
  SidebarType: DefaultSetting.sidebar_type.choices
}

export const useSetting = defineStore('setting', {
  state: () => ({...state}),
  getters: {
    theme_color_custom: (state) => state.setting.theme_color,
    card_style_value : (state) => state.setting.card_style.value,
    header_navbar_value : (state) => state.setting.header_navbar.value,
    footer_style_value : (state) => state.setting.footer_style.value,
    sidebar_color_value : (state) => state.setting.sidebar_color.value,
    sidebar_menu_style_value : (state) => state.setting.sidebar_menu_style.value,
    sidebar_type_value : (state) => state.setting.sidebar_type.value,
  },
  actions:{
    setSetting(payload){
      this.setting.card_style.value = payload.card_style.value
      this.setting.header_navbar.value = payload.header_navbar.value
      this.setting.footer_style.value = payload.footer_style.value
      this.setting.sidebar_color.value = payload.sidebar_color.value
      this.setting.sidebar_menu_style.value = payload.sidebar_menu_style.value
      this.setting.sidebar_type.value = payload.sidebar_type.value
      _.forEach(payload.theme_color.colors, (value, key) => {
        this.setting.theme_color.colors[key] = value
      })
      this.setting.theme_color.value = payload.theme_color.value
    },
    theme_color(payload) {
      if (typeof payload !== typeof undefined) {
        _.forEach(payload.colors, (value, key) => {
          this.setting.theme_color.colors[key] = value
        })
        this.setting.theme_color.value = payload.value
      }
      updateColorRootVar(this.setting.theme_scheme.value, this.setting.theme_color, Choices.ColorChoice)
    },
    header_navbar(payload){
      this.setting.header_navbar.value = payload
      updateSelectorClass('.iq-navbar',Choices.HeaderNavbarStyle, this.setting.header_navbar.value)
    },
    card_style(payload){
      this.setting.card_style.value = payload
      updateBodyClass(Choices.CardStyle, this.setting.card_style.value)
    },
    sidebar_color(payload){
      this.setting.sidebar_color.value = payload
      updateSelectorClass('[data-toggle="main-sidebar"]',Choices.MenuColor, this.setting.sidebar_color.value)
    },
    sidebar_type(payload){
      this.setting.sidebar_type.value = payload
      const payloadString = payload.toString();
      const newClass = payloadString.replace(/,/g,' ')
      updateSelectorClass('[data-toggle="main-sidebar"]',Choices.SidebarType, newClass)
    },
    sidebar_menu_style(payload){
      this.setting.sidebar_menu_style.value = payload
      updateSelectorClass('[data-toggle="main-sidebar"]',Choices.SidebarMenuStyle, this.setting.sidebar_menu_style.value)
    },
    sidebar_show(payload){
      this.setting.sidebar_show.value = payload
      updateSelectorClass('[data-toggle="main-sidebar"]',Choices.SidebarShow, this.setting.sidebar_show.value)
    },
    navbar_show(payload){
      this.setting.navbar_show.value = payload
      updateSelectorClass('.navbar',Choices.NavbarShow, this.setting.navbar_show.value)
    },
    footer_style(payload){
      this.setting.footer_style.value = payload
      updateSelectorClass('.footer',Choices.FooterStyle, this.setting.footer_style.value)
    },
    body_font_family(payload){
      if (typeof payload !== typeof undefined) {
        this.setting.body_font_family.value = payload
      }
      setFontFamily('body', this.setting.body_font_family.value)
    },
    heading_font_family(payload){
      if (typeof payload !== typeof undefined) {
        this.setting.heading_font_family.value = payload
      }
      setFontFamily('heading', this.setting.heading_font_family.value)
    }
  }
});
window.pinia = useSetting
