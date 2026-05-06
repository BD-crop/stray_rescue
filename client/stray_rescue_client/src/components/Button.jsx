function Button({
  children,
  variant = 'primary',
  size = 'md',
  className = '',
  icon,
  type = 'button',
  onClick,
}) {
  const base =
    'inline-flex items-center justify-center gap-2 rounded-2xl font-extrabold tracking-tight transition-all duration-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-orange-300 disabled:cursor-not-allowed disabled:opacity-60 will-change-transform'

  const variants = {
    primary:
      'bg-orange-500 text-white shadow-[0_16px_40px_rgba(249,115,22,0.28)] hover:-translate-y-0.5 hover:bg-orange-600 hover:shadow-[0_22px_46px_rgba(249,115,22,0.34)]',
    outline:
      'border-2 border-orange-300 bg-white/85 text-slate-800 hover:-translate-y-0.5 hover:border-orange-500 hover:bg-orange-50 hover:shadow-[0_14px_30px_rgba(249,115,22,0.08)]',
    ghost: 'bg-transparent text-slate-700 hover:bg-orange-50 hover:text-orange-600',
  }

  const sizes = {
    sm: 'min-h-10 px-4 text-sm',
    md: 'min-h-12 px-5 text-sm sm:text-base',
    lg: 'min-h-14 px-7 text-base sm:text-lg',
  }

  return (
    <button
      className={`${base} ${variants[variant]} ${sizes[size]} ${className}`}
      onClick={onClick}
      type={type}
    >
      {icon}
      <span>{children}</span>
    </button>
  )
}

export { Button }
