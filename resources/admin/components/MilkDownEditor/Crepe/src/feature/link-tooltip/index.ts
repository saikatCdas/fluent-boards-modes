import { configureLinkTooltip, linkTooltipConfig, linkTooltipPlugin } from '@milkdown/kit/component/link-tooltip'
import type { DefineFeature, Icon } from '../shared'
import { confirmIcon, editIcon, linkIcon, removeIcon } from '../../icons'

interface LinkTooltipConfig {
  linkIcon: Icon
  editButton: Icon
  removeButton: Icon
  confirmButton: Icon
  inputPlaceholder: string
  onCopyLink: (link: string) => void
}

export type LinkTooltipFeatureConfig = Partial<LinkTooltipConfig>

export const defineFeature: DefineFeature<LinkTooltipFeatureConfig> = (editor, config) => {
  editor
    .config(configureLinkTooltip)
    .config((ctx) => {
      ctx.update(linkTooltipConfig.key, prev => ({
        ...prev,
        // Provide empty strings for icons to avoid type errors
        // The visual link tooltip is hidden via CSS, but API is still available for toolbar
        linkIcon: '' as any,
        editButton: '' as any,
        removeButton: '' as any,
        confirmButton: '' as any,
        inputPlaceholder: config?.inputPlaceholder ?? 'Paste link...',
        onCopyLink: config?.onCopyLink ?? (() => {}),
      }))
    })
    .use(linkTooltipPlugin)
}
