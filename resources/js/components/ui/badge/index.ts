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
          'border-transparent bg-destructive font-medium text-destructive-foreground hover:bg-destructive/80',
        continuous:
          'border-transparent bg-yellow-50 font-medium text-yellow-700',
        progress:
          'border-transparent bg-blue-50 font-medium text-blue-700',
        success:
          'border-transparent bg-green-50 font-medium text-green-700',
        error:
          'border-transparent bg-red-50 font-medium text-red-700',
        outline: 'text-foreground',
      },
    },
    defaultVariants: {
      variant: 'default',
    },
  },
)

export type BadgeVariants = VariantProps<typeof badgeVariants>
