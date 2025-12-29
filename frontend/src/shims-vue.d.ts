declare module "*.vue" {
  import type { DefineComponent } from "vue"

  // Better: use `object` for props & data, `unknown` instead of `any`
  const component: DefineComponent<object, object, unknown>

  export default component
}
