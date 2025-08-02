export const userRoles = [
  {
    id: 'frontliner',
    label: 'Frontliner',
    class: {
      badge: 'bg-sky-100 dark:bg-sky-900/30',
      dot: 'bg-sky-700 dark:bg-sky-400',
      text: 'text-sky-700 dark:text-sky-400',
    },
  },
  {
    id: 'dispatcher',
    label: 'Dispatcher',
    class: {
      badge: 'bg-sky-100 dark:bg-sky-900/30',
      dot: 'bg-sky-700 dark:bg-sky-400',
      text: 'text-sky-700 dark:text-sky-400',
    },
  },
  {
    id: 'team leader',
    label: 'Team Leader',
    class: {
      badge: 'bg-lime-100 dark:bg-lime-900/30',
      dot: 'bg-lime-700 dark:bg-lime-400',
      text: 'text-lime-700 dark:text-lime-400',
    },
  },
  {
    id: 'head frontliner',
    label: 'Head Frontliner',
    class: {
      badge: 'bg-lime-100 dark:bg-lime-900/30',
      dot: 'bg-lime-700 dark:bg-lime-400',
      text: 'text-lime-700 dark:text-lime-400',
    },
  },
  {
    id: 'safety officer',
    label: 'Safety Officer',
    class: {
      badge: 'bg-lime-100 dark:bg-lime-900/30',
      dot: 'bg-lime-700 dark:bg-lime-400',
      text: 'text-lime-700 dark:text-lime-400',
    },
  },
  {
    id: 'human resource',
    label: 'Human Resource',
    class: {
      badge: 'bg-lime-100 dark:bg-lime-900/30',
      dot: 'bg-lime-700 dark:bg-lime-400',
      text: 'text-lime-700 dark:text-lime-400',
    },
  },
  {
    id: 'consultant',
    label: 'Consultant',
    class: {
      badge: 'bg-lime-100 dark:bg-lime-900/30',
      dot: 'bg-lime-700 dark:bg-lime-400',
      text: 'text-lime-700 dark:text-lime-400',
    },
  },
  {
    id: 'regular',
    label: 'Basic User',
    class: {
      badge: 'bg-sky-100 dark:bg-sky-900/30',
      dot: 'bg-sky-700 dark:bg-sky-400',
      text: 'text-sky-700 dark:text-sky-400',
    },
  },
  {
    id: 'it admin',
    label: 'IT Admin',
    class: {
      badge: 'bg-lime-100 dark:bg-lime-900/30',
      dot: 'bg-lime-700 dark:bg-lime-400',
      text: 'text-lime-700 dark:text-lime-400',
    },
  },
] as const

export type UserRoleType = (typeof userRoles)[number]['id']
