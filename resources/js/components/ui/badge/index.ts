import { cva, type VariantProps } from 'class-variance-authority'

export { default as Badge } from './Badge.vue'

export const badgeVariants = cva(
  'inline-flex items-center rounded-lg border px-2.5 py-0.5 text-xs font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2',
  {
    variants: {
      variant: {
        default:
          'border-transparent bg-primary text-primary-foreground hover:bg-primary/80',
        secondary:
          'border-transparent bg-secondary font-medium text-secondary-foreground hover:bg-secondary/80',
        destructive:
          'border-transparent bg-[#FEF2F2] font-medium text-[#DC2626]',
        continuous:
          'border-transparent bg-[#FEFCE8] font-medium text-[#A16207]',
        progress:
          'border-transparent bg-[#EFF6FF] font-medium text-[#1D4ED8]',
        success:
          'border-transparent bg-[#F0FDF4] font-medium text-[#15803D]',
        outline: 'text-foreground',
        wms:
          'border-transparent bg-[#ECFCCB] font-medium text-[#4D7C0F]',
        its:
          'border-transparent bg-[#DBEAFE] font-medium text-[#1E3A8A]',
      },
    },
    defaultVariants: {
      variant: 'default',
    },
  },
)

export type BadgeVariants = VariantProps<typeof badgeVariants>
