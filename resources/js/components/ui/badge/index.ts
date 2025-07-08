import { cva, type VariantProps } from 'class-variance-authority'

export { default as Badge } from './Badge.vue'

export const badgeVariants = cva(
  'inline-flex items-center rounded-lg border px-2.5 py-0.5 text-xs font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2',
  {
    variants: {
      variant: {
        default:
          'rounded-full border-transparent bg-primary text-primary-foreground hover:bg-primary/80',
        secondary:
          'rounded-full border-transparent bg-secondary font-medium text-secondary-foreground hover:bg-secondary/80',
        destructive:
          'rounded-full border-transparent bg-destructive font-medium text-destructive-foreground hover:bg-destructive/80',
        continuous: 
          'rounded-full border-transparent bg-yellow-50 font-medium text-yellow-700 dark:bg-[rgba(202,138,4,0.15)] dark:text-yellow-300 dark:border-yellow-500',
        progress: 
          'rounded-full border-transparent bg-blue-50 font-medium text-blue-700 dark:bg-[rgba(59,130,246,0.15)] dark:text-blue-300 dark:border-blue-500',
        success: 
          'rounded-full border-transparent bg-green-50 font-medium text-green-700 dark:bg-[rgba(34,197,94,0.15)] dark:text-green-200 dark:border-green-500',
        error: 
          'rounded-full border-transparent bg-red-50 font-medium text-red-700 dark:bg-[rgba(239,68,68,0.15)] dark:text-red-200 dark:border-red-500',
        outline: 'text-foreground',
      },
    },
    defaultVariants: {
      variant: 'default',
    },
  },
)

export type BadgeVariants = VariantProps<typeof badgeVariants>
