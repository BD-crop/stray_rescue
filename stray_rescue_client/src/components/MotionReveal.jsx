import { motion, useReducedMotion } from 'framer-motion'

function MotionReveal({
  children,
  className = '',
  delay = 0,
  duration = 0.55,
  amount = 0.22,
  y = 28,
}) {
  const reduceMotion = useReducedMotion()

  if (reduceMotion) {
    return <div className={className}>{children}</div>
  }

  return (
    <motion.div
      className={className}
      initial={{ opacity: 0, y }}
      transition={{ duration, delay, ease: [0.22, 1, 0.36, 1] }}
      viewport={{ once: true, amount }}
      whileInView={{ opacity: 1, y: 0 }}
    >
      {children}
    </motion.div>
  )
}

export { MotionReveal }
