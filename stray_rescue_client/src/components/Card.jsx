function Card({ children, className = '', hover = false }) {
  return (
    <article
      className={`overflow-hidden rounded-[28px] border border-slate-200/80 bg-white shadow-[0_20px_45px_rgba(15,23,42,0.06)] ${hover ? 'transition-transform duration-300 hover:-translate-y-2 hover:shadow-[0_24px_50px_rgba(249,115,22,0.12)]' : ''} ${className}`}
    >
      {children}
    </article>
  )
}

function CardHeader({ children, className = '' }) {
  return <div className={`p-6 sm:p-7 ${className}`}>{children}</div>
}

function CardContent({ children, className = '' }) {
  return <div className={`px-6 pb-6 sm:px-7 sm:pb-7 ${className}`}>{children}</div>
}

export { Card, CardHeader, CardContent }
